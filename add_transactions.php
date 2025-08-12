<?php
require 'config.php';

$data = json_decode(file_get_contents("php://input"));
$user_id = $data->user_id;
$client_id = $data->client_id;
$type = $data->type;
$amount = $data->amount;


$stmt = $pdo->prepare("INSERT INTO transactions (user_id, client_id, type, amount) VALUES (?, ?, ?,?)");
$stmt->execute([$user_id, $client_id, $type, $amount]);

echo json_encode(["status" => "success", "message" => "Client ajouté"]);
?>
