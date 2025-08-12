<?php
require 'config.php';

$user_id = $_GET['user_id'] ?? 0;
// echo password_hash("admin123", PASSWORD_BCRYPT);
$stmt = $pdo->prepare("SELECT * FROM clients WHERE user_id = ?");
$stmt->execute([$user_id]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
