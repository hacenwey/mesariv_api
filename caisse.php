<?php
require 'config.php';
$action = $_GET['action'] ?? '';

if ($action === 'get') {
    $user_id = intval($_GET['user_id'] ?? 0);
    $stmt = $pdo->prepare("SELECT * FROM caisse WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        $stmt2 = $pdo->prepare("INSERT INTO caisse (user_id,solde) VALUES (?, 0)");
        $stmt2->execute([$user_id]);
        $row = ['id'=>$pdo->lastInsertId(),'user_id'=>$user_id,'solde'=>0];
    }
    echo json_encode($row);
    exit;
}

$body = json_decode(file_get_contents("php://input"), true);

if ($action === 'update') {
    $user_id = intval($_GET['user_id'] ?? ($body['user_id'] ?? 0));
    $solde = floatval($body['solde'] ?? 0);
    $stmt = $pdo->prepare("UPDATE caisse SET solde = ?, updated_at = NOW() WHERE user_id = ?");
    $stmt->execute([$solde, $user_id]);
    echo json_encode(["status"=>"success"]);
    exit;
}

if ($action === 'add_movement') {
    // also insert into separate table if desired — simplified: update solde and optionally log
    $user_id = intval($_GET['user_id'] ?? ($body['user_id'] ?? 0));
    $type = $body['type'] ?? 'recette';
    $amount = floatval($body['amount'] ?? 0);
    if (!$user_id || !$amount) { http_response_code(400); echo json_encode(["status"=>"error","message"=>"Missing"]); exit; }
    // fetch current
    $stmt = $pdo->prepare("SELECT solde FROM caisse WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        $solde = ($type=='recette') ? $amount : -$amount;
        $stmt2 = $pdo->prepare("INSERT INTO caisse (user_id, solde) VALUES (?, ?)");
        $stmt2->execute([$user_id, $solde]);
    } else {
        $cur = floatval($row['solde']);
        $new = ($type=='recette') ? ($cur + $amount) : ($cur - $amount);
        $stmt2 = $pdo->prepare("UPDATE caisse SET solde = ?, updated_at = NOW() WHERE user_id = ?");
        $stmt2->execute([$new, $user_id]);
    }
    echo json_encode(["status"=>"success"]);
    exit;
}

echo json_encode(["status"=>"error","message"=>"No action"]);
?>
