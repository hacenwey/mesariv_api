<?php
require 'config.php';

$data = json_decode(file_get_contents("php://input"));

$action = $data->action ?? '';
if ($action === 'delete') {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT id FROM transactions WHERE client_id = ?");
    $stmt->execute([$id]);
    $transactions = $stmt->fetchAll(PDO::FETCH_COLUMN);
    if (!empty($transactions)) {
        $placeholders = implode(',', array_fill(0, count($transactions), '?'));
        $stmt = $pdo->prepare("DELETE FROM caisse WHERE transaction_id IN ($placeholders)");
        $stmt->execute($transactions);
    }
    
    
    $stmt = $pdo->prepare("DELETE FROM transactions WHERE client_id = ?");
    $stmt->execute([$id]);
    
    $stmt = $pdo->prepare("DELETE FROM clients WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(["status" => "success", "message" => "Client supprimé"]);
    exit;
}
$user_id = $data->user_id;
$name = $data->name;
$phone = $data->phone;

$stmt = $pdo->prepare("INSERT INTO clients (user_id, name, phone) VALUES (?, ?, ?)");
$stmt->execute([$user_id, $name, $phone]);

echo json_encode(["status" => "success", "message" => "Client ajouté"]);

