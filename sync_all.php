<?php
require 'config.php';
$body = json_decode(file_get_contents("php://input"), true);

// expected: body['user_remote_id'] or body['user_local_id'] and arrays
$user_remote_id = intval($body['user_remote_id'] ?? 0);
if (!$user_remote_id) {
    // optionally accept via GET param
    $user_remote_id = intval($_GET['user_id'] ?? 0);
}
if (!$user_remote_id) {
    http_response_code(400);
    echo json_encode(['status'=>'error','message'=>'user_remote_id required']);
    exit;
}

// 1) Push clients
if (!empty($body['clients'])) {
    foreach ($body['clients'] as $c) {
        // if client comes with id_remote -> update, else insert
        if (!empty($c['id_remote'])) {
            $stmt = $pdo->prepare("UPDATE clients SET name=?, phone=?, updated_at=NOW() WHERE id = ? AND user_id = ?");
            $stmt->execute([$c['name'],$c['phone'],$c['id_remote'],$user_remote_id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO clients (user_id,name,phone,created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$user_remote_id,$c['name'],$c['phone']]);
            // return mapping local_id -> new id_remote
            $c['_created_remote_id'] = $pdo->lastInsertId();
        }
    }
}

// 2) Push transactions
if (!empty($body['client_transactions'])) {
    foreach ($body['client_transactions'] as $t) {
        if (!empty($t['id_remote'])) {
            // update (basic)
            $stmt = $pdo->prepare("UPDATE transactions SET type=?, amount=?, note=?, created_at=NOW() WHERE id = ? AND user_id = ?");
            $stmt->execute([$t['type'],$t['amount'],$t['note'],$t['id_remote'],$user_remote_id]);
        } else {
            // Need mapping: client_remote_id should refer to server client id
            $client_remote = $t['client_remote_id'] ?? null;
            $stmt = $pdo->prepare("INSERT INTO transactions (client_id, user_id, type, amount, note, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$client_remote, $user_remote_id, $t['type'], $t['amount'], $t['note']]);
            $t['_created_remote_id'] = $pdo->lastInsertId();
        }
    }
}

// 3) Push caisse
if (!empty($body['caisse'])) {
    foreach ($body['caisse'] as $m) {
        if (!empty($m['id_remote'])) {
            // update existing remote movement if you have a movements table — simplified: ignore
        } else {
            // For caisse we instead update user's caisse (or log movement server-side)
            // Here we just insert a record into a 'caisse' table (mapping to user)
            $stmt = $pdo->prepare("INSERT INTO caisse (user_id, type, description, amount, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$user_remote_id, $m['type'], $m['description'], $m['amount']]);
            $m['_created_remote_id'] = $pdo->lastInsertId();
        }
    }
}

// 4) Pull: return data for this user
$clients = $pdo->prepare("SELECT * FROM clients WHERE user_id = ? ORDER BY id DESC");
$clients->execute([$user_remote_id]);
$clients_res = $clients->fetchAll(PDO::FETCH_ASSOC);

$transactions = $pdo->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY id DESC");
$transactions->execute([$user_remote_id]);
$transactions_res = $transactions->fetchAll(PDO::FETCH_ASSOC);

$caisse = $pdo->prepare("SELECT * FROM caisse WHERE user_id = ? ORDER BY id DESC");
$caisse->execute([$user_remote_id]);
$caisse_res = $caisse->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'status'=>'success',
    'clients'=>$clients_res,
    'client_transactions'=>$transactions_res,
    'caisse'=>$caisse_res
]);
?>
