<?php

require 'config.php';

$phone = $_GET['phone'] ?? '';
$password = $_GET['password'] ?? '';

if ($phone && $password) {
    $stmt = $pdo->prepare("INSERT INTO users (phone, password) VALUES (?, ?)");
    $stmt->execute([$phone, password_hash($password, PASSWORD_BCRYPT)]);
    echo json_encode(["status" => "success","user" => ["phone" => $phone, "password" => $password]]);
} else {
    echo json_encode(["status" => "error", "message" => "Missing phone or password"]);
}

