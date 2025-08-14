<?php
require 'config.php';

$data = json_decode(file_get_contents("php://input"));
$action = $data->action ?? '';

if ($action === 'delete') {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("DELETE FROM transactions WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(["status" => "success", "message" => "Client supprimé"]);
    exit;
}
$user_id = $data->user_id;
$client_id = $data->client_id;
$type = $data->type;
$amount = $data->amount;
$note = $data->note;



$stmt = $pdo->prepare("INSERT INTO transactions (user_id, client_id, type, amount, note) VALUES (?, ?, ?,?, ?)");
$stmt->execute([$user_id, $client_id, $type, $amount, $note]);

$stmt2 = $pdo->prepare("INSERT INTO caisse (user_id, solde,type, note) VALUES (?, ?,?,?)");
$stmt2->execute([$user_id, $amount, $type, $note]);
echo json_encode(["status" => "success", "message" => "Client ajouté"]);
?>
