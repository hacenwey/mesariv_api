<?php
require 'config.php';

$data = json_decode(file_get_contents("php://input"));
$user_id = $data->user_id;
$client_id = $data->client_id;
$type = $data->type;
$amount = $data->amount;
$note = $data->note;

$action = $data->action ?? '';

if ($action === 'delete') {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("DELETE FROM transactions WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(["status" => "success", "message" => "Client supprimé"]);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO transactions (user_id, client_id, type, amount, note) VALUES (?, ?, ?,?, ?)");
$stmt->execute([$user_id, $client_id, $type, $amount, $note]);

echo json_encode(["status" => "success", "message" => "Client ajouté"]);
?>
