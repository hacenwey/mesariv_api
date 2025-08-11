<?php
require 'config.php';

$user_id = $_GET['user_id'] ?? 0;
$client_id = $_GET['client_id'] ?? null;

if ($client_id) {
    $stmt = $pdo->prepare("SELECT * FROM transactions WHERE user_id = ? AND client_id = ?");
    $stmt->execute([$user_id, $client_id]);
} else {
    $stmt = $pdo->prepare("SELECT * FROM transactions WHERE user_id = ?");
    $stmt->execute([$user_id]);
}

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
