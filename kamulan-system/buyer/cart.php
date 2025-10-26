<?php
require_once(__DIR__ . '/../config/session.php');
require_once(__DIR__ . '/../config/db.php');

if(!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
$cart = &$_SESSION['cart'];

// ---------- AJAX REMOVE ----------
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['remove_id'])){
    header('Content-Type: application/json');
    $remove_id = intval($_POST['remove_id']);

    if(isset($cart[$remove_id])){
        unset($cart[$remove_id]);

        // Recalculate subtotal
        $subtotal = 0;
        foreach($cart as $c) $subtotal += $c['price']*$c['qty'];

        echo json_encode([
            'success'=>true,
            'subtotal'=>number_format($subtotal,2),
            'empty'=>empty($cart)
        ]);
    } else {
        echo json_encode(['success'=>false]);
    }
    exit; // ✅ mahalaga: Huwag tanggalin ito
}


// ---------- ADD TO CART ----------
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['id']) && !isset($_POST['place_order'])){
    $id = intval($_POST['id']);
    $qty = max(1,intval($_POST['qty'] ?? 1));
    $stmt = $pdo->prepare('SELECT * FROM menu_items WHERE id=?');
    $stmt->execute([$id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if($item){
        if(isset($cart[$id])) $cart[$id]['qty'] += $qty;
        else $cart[$id] = [
            'id'=>$item['id'],
            'name'=>$item['name'],
            'price'=>$item['price'],
            'qty'=>$qty,
            'branch_id'=>$item['branch_id'] ?? null
        ];
    }

    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])==='xmlhttprequest'){
        echo json_encode(['success'=>true,'cart'=>$cart]);
        exit;
    } else {
        header('Location: cart.php'); exit;
    }
}


// ---------- PLACE ORDER ----------
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    if (empty($_SESSION['cart'])) {
        $msg = 'Your cart is empty!';
    } else {
        require_login();

        $branch_id = intval($_POST['branch_id']);
        $payment_method = $_POST['payment_method'];
        $address = trim($_POST['address'] ?? '');

        // compute subtotal
        $subtotal = 0;
        foreach ($_SESSION['cart'] as $c) $subtotal += $c['price'] * $c['qty'];

        // delivery fee
        $delivery_fee = 0; $branch_name = '';
        switch ($branch_id) {
            case 1: $delivery_fee = 25; $branch_name = 'Rizal'; break;
            case 2: $delivery_fee = 35; $branch_name = 'Zulueta'; break;
            case 3: $delivery_fee = 35; $branch_name = 'Mabini'; break;
        }
        $total = $subtotal + $delivery_fee;

        // insert into orders
        $stmt = $pdo->prepare('INSERT INTO orders (user_id, branch_id, address, total, payment_method, status, created_at)
                               VALUES (?, ?, ?, ?, ?, "Pending", NOW())');
        $stmt->execute([$_SESSION['user']['id'], $branch_id, $address, $total, $payment_method]);
        $order_id = $pdo->lastInsertId();

        // insert order items
        $istmt = $pdo->prepare('INSERT INTO order_items (order_id, menu_item_id, qty, price) VALUES (?, ?, ?, ?)');
        foreach ($_SESSION['cart'] as $c) $istmt->execute([$order_id, $c['id'], $c['qty'], $c['price']]);

        // clear cart
        unset($_SESSION['cart']);
        $msg = "✅ Order placed successfully!<br>
                Order ID: <b>{$order_id}</b><br>
                Branch: <b>{$branch_name}</b><br>
                Delivery Fee: ₱{$delivery_fee}<br>
                Total: ₱" . number_format($total, 2);
    }
}
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Cart</title>
<link rel="stylesheet" href="/kamulan-system/assets/css/style.css">
<style>
body { background:#f5f5f5; font-family:Arial,sans-serif; }
main.container { max-width:900px;margin:50px auto;background:white;padding:20px;border-radius:10px; }
table { width:100%;border-collapse:collapse;margin-bottom:20px; }
th,td { padding:10px;border-bottom:1px solid #ddd;text-align:center; }
th { background:#ffa726;color:white; }
.button { background:#ff7043;color:white;border:none;padding:10px 20px;border-radius:5px;cursor:pointer; }
.button:hover { background:#f4511e; }
.success { background:#d4edda;color:#155724;padding:10px;border-radius:5px;margin-bottom:20px; }
.remove-btn { background:#e53935;color:white;border:none;padding:5px 10px;border-radius:4px;cursor:pointer; }
.remove-btn:hover { background:#c62828; }
.nav-buttons { display:flex;justify-content:space-between;align-items:center;margin-top:20px; }
</style>
</head>
<body>
<?php include(__DIR__ . '/../_partials/header.php'); ?>
<main class="container">
<h2>Your Cart</h2>
<?php if($msg) echo "<p class='success'>$msg</p>"; ?>

<?php if(empty($_SESSION['cart'])): ?>
<p>Your cart is empty.</p>
<a href="/kamulan-system/buyer/menu.php" class="button">Continue Ordering</a>
<?php else: ?>
<table>
<tr><th>Item</th><th>Qty</th><th>Price</th><th>Subtotal</th><th></th></tr>
<?php $subtotal=0; foreach($_SESSION['cart'] as $c):
$sub=$c['price']*$c['qty']; $subtotal+=$sub; ?>
<tr data-id="<?= intval($c['id']) ?>">

  <td><?= htmlspecialchars($c['name']) ?></td>
  <td><?= intval($c['qty']) ?></td>
  <td>₱<?= number_format($c['price'],2) ?></td>
  <td>₱<span class="item-subtotal"><?= number_format($sub,2) ?></span></td>
  <td><button class="remove-btn" data-id="<?= $c['id'] ?>">Remove</button></td>
</tr>
<?php endforeach; ?>
</table>

<form method="post">
<p><b>Subtotal:</b> ₱<span id="cart-subtotal"><?= number_format($subtotal,2) ?></span></p>

<!-- Branch selection -->
<p>
  <label for="branch_id"><b>Select Branch:</b></label>
  <select name="branch_id" id="branch_id" required>
    <option value="">--Choose Branch--</option>
    <option value="1">Rizal</option>
    <option value="2">Zulueta</option>
    <option value="3">Mabini</option>
  </select>
</p>

<!-- Payment method -->
<p>
  <label for="payment_method"><b>Payment Method:</b></label>
  <select name="payment_method" id="payment_method" required>
    <option value="">--Choose Payment--</option>
    <option value="Cash">COD</option>
    <option value="GCash">GCash</option>
  </select>
</p>

<!-- Optional: Delivery address -->
<p>
  <label for="address"><b>Delivery Address:</b></label><br>
  <textarea name="address" id="address" rows="2" style="width:100%;" required></textarea>
</p>

<div class="nav-buttons">
<a href="/kamulan-system/buyer/menu.php" class="button">🛒 Continue Ordering</a>
<button class="button" name="place_order" type="submit">Place Order</button>
</div>
</form>

<?php endif; ?>
</main>
<?php include(__DIR__ . '/../_partials/footer.php'); ?>

<script>
document.querySelectorAll('.remove-btn').forEach(btn=>{
    btn.addEventListener('click', function(){
        const id = parseInt(this.getAttribute('data-id'));


        fetch('cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'remove_id=' + encodeURIComponent(id)
        })
        .then(res => res.json())
        .then(data => {
            if(data.success){
                const row = document.querySelector('tr[data-id="'+id+'"]');
                if(row) row.remove();

                // Update subtotal
                document.getElementById('cart-subtotal').textContent = data.subtotal;

                // If cart is empty
                if(data.empty){
                    document.querySelector('main.container').innerHTML =
                        '<p>Your cart is empty.</p><a href="/kamulan-system/buyer/menu.php" class="button">Continue Ordering</a>';
                }
            } else {
                alert('Error removing item!');
            }
        })
        .catch(err=>{
            console.error(err);
            alert('Error removing item!');
        });
    });
});

</script>
</body>
</html>
