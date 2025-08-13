<?php
require 'config.php';

$user_id = $_GET['user_id'] ?? 0;

$stmt = $pdo->prepare("SELECT c.*, COUNT(t.id) as transactions, SUM(t.amount) as total_amount FROM clients c LEFT JOIN transactions t ON c.id = t.client_id WHERE c.user_id = ? GROUP BY c.id");
$stmt->execute([$user_id]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
