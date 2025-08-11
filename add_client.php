<?php
require 'config.php';

$data = json_decode(file_get_contents("php://input"));
$user_id = $data->user_id;
$name = $data->name;
$phone = $data->phone;

$stmt = $pdo->prepare("INSERT INTO clients (user_id, name, phone) VALUES (?, ?, ?)");
$stmt->execute([$user_id, $name, $phone]);

echo json_encode(["status" => "success", "message" => "Client ajouté"]);
?>
