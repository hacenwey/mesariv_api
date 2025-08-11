<?php
require 'config.php';

$user_id = $_GET['user_id'] ?? 0;
echo 'starting with user_id ' . $user_id . "\n";
$stmt = $pdo->prepare("SELECT * FROM clients WHERE user_id = ?");
$stmt->execute([$user_id]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
