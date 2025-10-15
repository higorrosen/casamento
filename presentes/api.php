<?php
header('Content-Type: application/json');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

$productsFile = 'produtos.json';
$csvFile = 'presentes_recebidos.csv';
$action = $_GET['action'] ?? '';

// ==== GET PRODUTOS ====
if ($action === 'get') {
    if (!file_exists($productsFile)) {
        http_response_code(404);
        echo json_encode(['error' => 'Arquivo não encontrado']);
        exit;
    }
    
    $products = json_decode(file_get_contents($productsFile), true);
    echo json_encode($products);
    exit;
}

// ==== CONFIRMAR PRESENTE ====
if ($action === 'confirm' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validação
    if (!isset($data['product_id'], $data['product_name'], $data['quantity'], $data['name'], $data['email'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
        exit;
    }
    
    $productId = $data['product_id'];
    $productName = $data['product_name'];
    $quantity = (int)$data['quantity'];
    $name = $data['name'];
    $email = $data['email'];
    
    // Carregar produtos
    $products = json_decode(file_get_contents($productsFile), true);
    
    // Encontrar e atualizar produto
    $found = false;
    foreach ($products as &$product) {
        if ($product['id'] == $productId) {
            $found = true;
            
            if ($product['quantity'] < $quantity) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Quantidade não disponível']);
                exit;
            }
            
            $product['quantity'] -= $quantity;
            break;
        }
    }
    
    if (!$found) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Produto não encontrado']);
        exit;
    }
    
    // Salvar produtos atualizados
    file_put_contents($productsFile, json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    // Salvar no CSV
    $csv = fopen($csvFile, 'a');
    fputcsv($csv, [
        date('Y-m-d H:i:s'),
        $name,
        $email,
        $productName,
        $quantity
    ]);
    fclose($csv);
    
    // Enviar e-mails
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'higor.cabral95@gmail.com';
        $mail->Password = 'wxyl irpo dkmb jpkw';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;
        $mail->CharSet = 'UTF-8';
        
        // Email para os noivos
        $mail->setFrom('higor.cabral95@gmail.com', 'Site Casamento');
        $mail->addAddress('higor.cabral95@gmail.com');
        $mail->isHTML(true);
        $mail->Subject = "Novo Presente: {$productName}";
        $mail->Body = "Você recebeu um presente!<br><br>
                      <b>Presente:</b> {$productName}<br>
                      <b>Quantidade:</b> {$quantity}<br>
                      <b>De:</b> {$name}<br>
                      <b>E-mail:</b> {$email}";
        $mail->send();
        
        // Email para o convidado
        $mail->clearAddresses();
        $mail->addAddress($email);
        $mail->Subject = 'Obrigado pelo presente!';
        $mail->Body = "Olá, {$name}!<br><br>
                      Recebemos sua confirmação do presente: <b>{$productName}</b>.<br><br>
                      Ficamos muito felizes! Mal podemos esperar para celebrar com você!<br><br>
                      Com carinho,<br>
                      <b>Ana Caroline e Higor</b>";
        $mail->send();
    } catch (Exception $e) {
        error_log("Erro ao enviar email: " . $mail->ErrorInfo);
    }
    
    echo json_encode(['success' => true, 'message' => 'Presente confirmado!']);
    exit;
}

http_response_code(400);
echo json_encode(['success' => false, 'message' => 'Ação inválida']);
?>