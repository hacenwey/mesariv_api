<?php
require 'config.php';
$action = $_GET['action'] ?? '';
if ($action === 'get') {
    $user_id = intval($_GET['user_id'] ?? 0);
    if (!$user_id) {
        http_response_code(400);
        echo json_encode(["status"=>"error","message"=>"Missing user_id"]);
        exit;
    }
    $stmt = $pdo->prepare("SELECT c.name as client_name, c.* FROM caisse c LEFT JOIN transactions t ON c.transaction_id = t.id LEFT JOIN clients cl ON t.client_id = cl.id WHERE c.user_id = ?");
    $stmt->execute([$user_id]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as &$row) {
        if ($row['transaction_id'] !== null) {
            $row['client_name'] = $row['client_name'] ?? '';
        }
    }
    echo json_encode($rows);
    exit;
}

if ($action === 'get_all_caisses') {
    $user_id = intval($_GET['user_id'] ?? 0);
    if (!$user_id) {
        http_response_code(400);
        echo json_encode(["status"=>"error","message"=>"Missing user_id"]);
        exit;
    }
    $stmt = $pdo->prepare("SELECT type, SUM(solde) as amount FROM caisse WHERE user_id = ? GROUP BY type");
    $stmt->execute([$user_id]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $res = [
        'restant' => 0,
        'all_recettes' => 0,
        'all_depenses' => 0
    ];
    foreach ($rows as $row) {
        if ($row['type'] === 'recette') {
            $res['all_recettes'] = $row['amount'];
        } else {
            $res['all_depenses'] = $row['amount'];
        }
    }
    $res['restant'] = $res['all_recettes'] - $res['all_depenses'];
    echo json_encode($res);
    exit;
}

$body = json_decode(file_get_contents("php://input"), true);


if ($action === 'delete') {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("DELETE FROM caisse WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(["status" => "success", "message" => "Caisse supprimé"]);
    exit;
}
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
    $note = $body['note'] ?? '';
    if (!$user_id || !$amount) { http_response_code(400); echo json_encode(["status"=>"error","message"=>"Missing"]); exit; }

        $stmt2 = $pdo->prepare("INSERT INTO caisse (user_id, solde,type, note) VALUES (?, ?,?,?)");
        $stmt2->execute([$user_id, $amount, $type, $note]);
   
    echo json_encode(["status"=>"success"]);
    exit;
}

echo json_encode(["status"=>"error","message"=>"No action"]);
?>
