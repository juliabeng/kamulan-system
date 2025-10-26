<?php
require_once(__DIR__.'/../config/session.php');
require_once(__DIR__.'/../config/db.php');

if(!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
$cart = &$_SESSION['cart'];

if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['id'])){
    $id = (int)$_POST['id'];
    $qty = max(1,intval($_POST['qty'] ?? 1)); // ignore 0

    // Fetch item
    $stmt = $pdo->prepare('SELECT * FROM menu_items WHERE id=?');
    $stmt->execute([$id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if($item){
        if(isset($cart[$id])) $cart[$id]['qty'] += $qty;
        else $cart[$id] = [
            'id'=>$item['id'],
            'name'=>$item['name'],
            'price'=>$item['price'],
            'qty'=>$qty
        ];
    }

    // Recalculate subtotal
    $subtotal = 0;
    foreach($cart as $c) $subtotal += $c['price'] * $c['qty'];

    // Return JSON
    header('Content-Type: application/json');
    echo json_encode([
        'success'=>true,
        'cart'=>array_values($cart), // reset keys for JS foreach
        'subtotal'=>number_format($subtotal,2)
    ]);
    exit;
}
