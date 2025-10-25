<?php
require_once(__DIR__ . '/../config/session.php');
require_once(__DIR__ . '/../config/db.php');
require_role(['staff']);
if($_SERVER['REQUEST_METHOD']==='POST'){
    $order_id = intval($_POST['order_id']); $status = $_POST['status'];
    $stmt = $pdo->prepare('UPDATE orders SET status=? WHERE id=?'); $stmt->execute([$status,$order_id]);
}
header('Location: dashboard.php'); exit;
?>
