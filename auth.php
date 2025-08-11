<?php
require 'config.php';

$action = $_GET['action'] ?? '';

if ($action == 'register') {
    $data = json_decode(file_get_contents("php://input"), true);
    $phone = $data['phone'];
    $password = password_hash($data['password'], PASSWORD_BCRYPT);
    $name = $data['name'];

    $stmt = $pdo->prepare("INSERT INTO users (phone, password, name) VALUES (?, ?, ?)");
    $stmt->execute([$phone, $password, $name]);
    echo json_encode(["status" => "success"]);
}

if ($action == 'login') {
    $data = json_decode(file_get_contents("php://input"), true);
    $phone = $data['phone'];
    $password = $data['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE phone = ?");
    $stmt->execute([$phone]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        echo json_encode(["status" => "success", "user" => $user]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid credentials"]);
    }
}
?>
