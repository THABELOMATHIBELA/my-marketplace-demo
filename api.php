<?php
// api.php — JSON endpoint for cart actions
session_start();
require 'inc/db.php';
$input = json_decode(file_get_contents('php://input'), true);
header('Content-Type: application/json');

if(!$input || empty($input['action'])) {
    echo json_encode(['success'=>false,'error'=>'no action']);
    exit;
}
$action = $input['action'];

if($action === 'add'){
    $id = (int)($input['id'] ?? 0);
    $qty = max(1, (int)($input['qty'] ?? 1));
    if(!$id){ echo json_encode(['success'=>false,'error'=>'no id']); exit; }
    // optional: check stock
    $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + $qty;
    $count = array_sum($_SESSION['cart']);
    echo json_encode(['success'=>true, 'count'=>$count]);
    exit;
}

if($action === 'remove'){
    $id = (int)($input['id'] ?? 0);
    if(isset($_SESSION['cart'][$id])) unset($_SESSION['cart'][$id]);
    echo json_encode(['success'=>true, 'count'=>array_sum($_SESSION['cart'] ?? [])]);
    exit;
}

if($action === 'clear'){
    unset($_SESSION['cart']);
    echo json_encode(['success'=>true,'count'=>0]);
    exit;
}

echo json_encode(['success'=>false,'error'=>'unknown action']);
