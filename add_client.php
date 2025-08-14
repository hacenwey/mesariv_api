<?php
require 'config.php';

$data = json_decode(file_get_contents("php://input"));

$action = $data->action ?? '';

if ($action === 'delete') {
    $id = intval($_GET['id']);
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
?>
