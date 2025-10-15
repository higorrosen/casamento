<?php
// Importa as classes do PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Carrega os arquivos do PHPMailer
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

// Define que a resposta será no formato JSON
header('Content-Type: application/json');

$csvFile = 'status_presenca.csv';
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

// Valida se os nomes e o e-mail foram recebidos
if (isset($data['names']) && is_array($data['names']) && !empty($data['names']) && isset($data['email']) && filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    
    $guestEmail = $data['email'];
    
    // 1. SALVAR A PRESENÇA NO ARQUIVO CSV (AGORA COM O E-MAIL)
    try {
        $file = @fopen($csvFile, 'a');
        if ($file === false) {
            throw new Exception('Não foi possível abrir o arquivo CSV para escrita.');
        }

        foreach ($data['names'] as $name) {
            // Adicionamos o e-mail do convidado em cada linha para seus registros
            fputcsv($file, [trim($name), date('Y-m-d H:i:s'), $guestEmail], ',', '"', '\\');
        }
        fclose($file);

        // ===== ADICIONA LINHA COM TOTAL DE CONFIRMADOS =====
        // Conta quantas pessoas únicas já confirmaram (excluindo a linha de total)
        $confirmados = [];
        if (file_exists($csvFile) && filesize($csvFile) > 0) {
            $linhas = file($csvFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($linhas as $linha) {
                $dados = str_getcsv($linha);
                // Ignora linhas vazias e a linha de TOTAL
                if (!empty($dados[0]) && $dados[0] !== 'TOTAL CONFIRMADOS') {
                    $confirmados[trim($dados[0])] = true;
                }
            }
        }
        
        $totalConfirmados = count($confirmados);
        
        // Remove a última linha de total (se existir)
        if (file_exists($csvFile) && filesize($csvFile) > 0) {
            $linhas = file($csvFile, FILE_IGNORE_NEW_LINES);
            if (count($linhas) > 0) {
                // Remove linha de total se for a última
                if (strpos($linhas[count($linhas) - 1], 'TOTAL CONFIRMADOS') !== false) {
                    array_pop($linhas);
                    file_put_contents($csvFile, implode(PHP_EOL, $linhas) . PHP_EOL);
                }
            }
        }
        
        // Adiciona nova linha de total
        $file = fopen($csvFile, 'a');
        fputcsv($file, ['TOTAL CONFIRMADOS', $totalConfirmados, date('Y-m-d H:i:s')], ',', '"', '\\');
        fclose($file);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Falha ao salvar no CSV: ' . $e->getMessage()]);
        exit;
    }

    // --- Início do processo de envio de e-mails ---
    $mail = new PHPMailer(true);

    try {
        // Configurações do Servidor SMTP (suas credenciais)
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'higor.cabral95@gmail.com';
        $mail->Password   = 'wxyl irpo dkmb jpkw';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;
        $mail->CharSet    = 'UTF-8';

        // --- 2. ENVIAR E-MAIL DE NOTIFICAÇÃO PARA O ADMIN (VOCÊ) ---
        $mail->setFrom('higor.cabral95@gmail.com', 'Site Casamento');
        $mail->addAddress('higor.cabral95@gmail.com'); // E-mail que receberá a notificação

        // Conteúdo do E-mail
        $mail->isHTML(true);
        $mail->Subject = 'Nova Confirmação de Presença!';
        $nomesConfirmados = implode(', ', $data['names']);
        $quantidadePessoas = count($data['names']);
        $mail->Body    = "<h2>Nova Confirmação Recebida!</h2>" .
                        "<p><strong>Quantidade de pessoas confirmadas nesta solicitação:</strong> " . $quantidadePessoas . "</p>" .
                        "<p><strong>Nomes:</strong> " . htmlspecialchars($nomesConfirmados) . "</p>" .
                        "<p><strong>E-mail do convidado:</strong> " . htmlspecialchars($guestEmail) . "</p>" .
                        "<hr>" .
                        "<p><strong>TOTAL GERAL DE CONFIRMADOS ATÉ AGORA:</strong> " . $totalConfirmados . " pessoas</p>";
        
        $mail->send();

        // --- 3. ENVIAR E-MAIL DE CONFIRMAÇÃO PARA O CONVIDADO ---
        
        // Limpa os destinatários anteriores para enviar o novo e-mail
        $mail->clearAddresses();

        // Adiciona o e-mail do convidado como novo destinatário
        $mail->addAddress($guestEmail);

        // Novo conteúdo do e-mail
        $mail->Subject = 'Presença Confirmada - Casamento Ana Caroline e Higor';
        $mail->Body    = "Olá!<br><br>Recebemos sua confirmação de presença para o nosso casamento. Estamos muito felizes em celebrar este momento com você!<br><br><strong>Nomes confirmados:</strong><br>" . htmlspecialchars($nomesConfirmados) . "<br><br>Quantidade: " . $quantidadePessoas . " pessoa(s)<br><br>Com carinho,<br><strong>Ana Caroline e Higor</strong>";
        
        $mail->send();

    } catch (Exception $e) {
        // Se qualquer um dos e-mails falhar, não impede a confirmação de sucesso do usuário.
        // Apenas registra o erro no log do servidor.
        error_log("PHPMailer Error: {$mail->ErrorInfo}");
    }

    // 4. RETORNAR SUCESSO PARA A PÁGINA
    echo json_encode([
        'status' => 'success', 
        'message' => 'Dados salvos com sucesso.',
        'total_confirmados' => $totalConfirmados
    ]);

} else {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Dados inválidos recebidos (nomes ou e-mail faltando).']);
}
?>