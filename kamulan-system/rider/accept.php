<?php
require_once(__DIR__ . '/../config/session.php');
require_once(__DIR__ . '/../config/db.php');
require_role(['rider']);
if($_SERVER['REQUEST_METHOD']==='POST'){
    $order_id = intval($_POST['order_id']);
    $stmt = $pdo->prepare('UPDATE orders SET rider_id=?, status=? WHERE id=?');
    $stmt->execute([$_SESSION['user']['id'],'Out for Delivery',$order_id]);
}
header('Location: dashboard.php'); exit;
?>
