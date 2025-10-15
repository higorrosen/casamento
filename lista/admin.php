<?php
// Inicia a sessão para "lembrar" que o admin está logado
session_start();

// --- CONFIGURAÇÃO ---
// !!! TROQUE ESTA SENHA POR UMA SENHA SEGURA !!!
define('SENHA_ADMIN', 'casamento2026'); 
// --------------------

$erro_login = '';

// Verifica se o formulário de login foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['senha']) && $_POST['senha'] === SENHA_ADMIN) {
        // Se a senha estiver correta, marca na sessão que o login foi feito
        $_SESSION['admin_logado'] = true;
        // Redireciona para a própria página para limpar os dados do POST
        header('Location: admin.php');
        exit;
    } else {
        $erro_login = 'Senha incorreta!';
    }
}

// Verifica se o admin pediu para fazer logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

// Verifica se o admin pediu para zerar a lista
if (isset($_POST['zerar_lista']) && isset($_SESSION['admin_logado']) && $_SESSION['admin_logado'] === true) {
    $statusPresencaFile = 'status_presenca.csv';
    if (file_exists($statusPresencaFile)) {
        // Cria um backup antes de zerar (somente se tiver conteúdo)
        if (filesize($statusPresencaFile) > 0) {
            $backup = 'backup_presenca_' . date('Y-m-d_H-i-s') . '.csv';
            copy($statusPresencaFile, $backup);
            $mensagem_sucesso = 'Lista zerada com sucesso! Backup salvo em: ' . $backup;
        } else {
            $mensagem_sucesso = 'Lista zerada com sucesso!';
        }
        // Zera o arquivo completamente
        file_put_contents($statusPresencaFile, '');
    } else {
        // Se o arquivo não existe, cria vazio
        file_put_contents($statusPresencaFile, '');
        $mensagem_sucesso = 'Lista zerada com sucesso!';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Administrador</title>
    <link href="https://fonts.googleapis.com/css2?family=Marcellus&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400&display=swap" rel="stylesheet">
    <style>
        /* Estilo básico para a página de admin, consistente com o site */
        body { font-family: 'Playfair Display', serif; background-color: #fdfaf6; color: #5d5d5d; text-align: center; padding: 40px; }
        .container { max-width: 800px; margin: 0 auto; background: #fff; border: 1px solid #e0dcd7; padding: 40px; }
        h1 { font-family: 'Marcellus', serif; color: #8c7b73; margin-bottom: 30px; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 20px; margin: 30px 0; }
        .stat-card { background: #fdfaf6; padding: 20px; border: 1px solid #e0dcd7; }
        .stat-number { font-size: 2.5em; font-weight: bold; color: #8c7b73; }
        .stat-label { margin-top: 10px; font-size: 0.9em; }
        input { width: 100%; max-width: 300px; padding: 10px; font-size: 1em; border: 1px solid #e0dcd7; margin-bottom: 10px; }
        button { font-family: 'Marcellus', serif; text-transform: uppercase; padding: 10px 20px; border: 1px solid #8c7b73; background: #8c7b73; color: white; cursor: pointer; }
        .error { color: #c00; margin-bottom: 15px; }
        a { color: #8c7b73; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .links { margin-top: 30px; }
        .links a { display: inline-block; margin: 10px; padding: 10px 20px; border: 1px solid #8c7b73; }
        .danger-zone { margin-top: 40px; padding: 20px; border: 2px solid #c00; background: #fff5f5; }
        .danger-zone h3 { color: #c00; margin-bottom: 15px; }
        .btn-danger { background: #c00; color: white; border: 1px solid #c00; padding: 10px 20px; cursor: pointer; font-family: 'Marcellus', serif; text-transform: uppercase; }
        .btn-danger:hover { background: #a00; }
        .mensagem-sucesso { background: #d4edda; color: #155724; padding: 15px; border: 1px solid #c3e6cb; margin-bottom: 20px; border-radius: 4px; }
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
        .modal-content { background: #fff; margin: 15% auto; padding: 30px; border: 1px solid #888; width: 80%; max-width: 500px; text-align: center; }
        .modal-buttons { margin-top: 20px; }
        .modal-buttons button { margin: 0 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Painel do Administrador</h1>

        <?php if (isset($_SESSION['admin_logado']) && $_SESSION['admin_logado'] === true): ?>
            
            <?php if (isset($mensagem_sucesso)): ?>
                <div class="mensagem-sucesso">
                    ✓ <?php echo $mensagem_sucesso; ?>
                </div>
            <?php endif; ?>
            
            <?php
                $listaConvidadosFile = 'lista_convidados.csv';
                $statusPresencaFile = 'status_presenca.csv';

                // 1. Calcula o total de convidados da lista original
                $totalConvidados = 0;
                $todosConvidados = []; // Array para armazenar todos os nomes
                
                if (file_exists($listaConvidadosFile) && ($handle = fopen($listaConvidadosFile, "r")) !== FALSE) {
                    $header = fgetcsv($handle); // Pula o cabeçalho
                    
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        // Nome principal
                        if (!empty($data[0])) {
                            $nomePrincipal = trim($data[0]);
                            $todosConvidados[] = $nomePrincipal;
                            $totalConvidados++;
                        }
                        
                        // Acompanhantes (segundo campo)
                        if (!empty($data[1])) {
                            $acompanhantes = explode(';', $data[1]);
                            foreach ($acompanhantes as $acompanhante) {
                                $acompanhante = trim($acompanhante);
                                if ($acompanhante !== '') {
                                    $todosConvidados[] = $acompanhante;
                                    $totalConvidados++;
                                }
                            }
                        }
                    }
                    fclose($handle);
                }

                // 2. Lê os presentes (apenas nomes únicos, excluindo linha de TOTAL)
                $presentesUnicos = [];
                if (file_exists($statusPresencaFile) && filesize($statusPresencaFile) > 0) {
                    if (($handle = fopen($statusPresencaFile, "r")) !== FALSE) {
                        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                            // Ignora linhas vazias e a linha de TOTAL CONFIRMADOS
                            if (!empty($data[0]) && $data[0] !== 'TOTAL CONFIRMADOS') {
                                $nome = trim($data[0]);
                                // Usa o nome como chave para garantir unicidade
                                $presentesUnicos[$nome] = true;
                            }
                        }
                        fclose($handle);
                    }
                }
                
                $totalPresentes = count($presentesUnicos);

                // 3. Calcula o resto
                $totalAusentes = $totalConvidados - $totalPresentes;
                $percentual = ($totalConvidados > 0) ? round(($totalPresentes / $totalConvidados) * 100) : 0;
            ?>

            <div class="stats">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $totalConvidados; ?></div>
                    <div class="stat-label">Total de Convidados</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $totalPresentes; ?></div>
                    <div class="stat-label">Confirmados</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $totalAusentes; ?></div>
                    <div class="stat-label">Pendentes</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $percentual; ?>%</div>
                    <div class="stat-label">Taxa de Confirmação</div>
                </div>
            </div>

            <div class="links">
                <a href="status_presenca.csv" download>Baixar Lista de Confirmados</a>
                <br><br>
                <a href="?logout=true">Sair</a>
                <br><br>
                <button type="button" class="btn-danger" onclick="confirmarZerar()">Zerar Lista de Confirmações</button>
            </div>


            <!-- Modal de Confirmação -->
            <div id="modalConfirmacao" class="modal">
                <div class="modal-content">
                    <h3 style="color: #c00;">ATENÇÃO!</h3>
                    <p>Você tem certeza que deseja zerar TODA a lista de confirmações?</p>
                    <p><strong>Um backup será criado automaticamente.</strong></p>
                    <div class="modal-buttons">
                        <form method="POST" style="display: inline;">
                            <button type="submit" name="zerar_lista" class="btn-danger">Sim, Zerar Lista</button>
                        </form>
                        <button type="button" class="btn-secondary" onclick="fecharModal()">Cancelar</button>
                    </div>
                </div>
            </div>

            <script>
                function confirmarZerar() {
                    document.getElementById('modalConfirmacao').style.display = 'block';
                }
                
                function fecharModal() {
                    document.getElementById('modalConfirmacao').style.display = 'none';
                }
                
                // Fecha o modal se clicar fora dele
                window.onclick = function(event) {
                    var modal = document.getElementById('modalConfirmacao');
                    if (event.target == modal) {
                        fecharModal();
                    }
                }
            </script>

        <?php else: ?>

            <form method="POST" action="admin.php">
                <p>Por favor, insira a senha para acessar.</p>
                <br>
                <input type="password" name="senha" id="senha" required placeholder="Digite a senha">
                <br><br>
                <button type="submit">Entrar</button>
                <?php if ($erro_login): ?>
                    <p class="error"><?php echo $erro_login; ?></p>
                <?php endif; ?>
            </form>

        <?php endif; ?>

    </div>
</body>
</html>