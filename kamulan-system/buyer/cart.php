<?php
require_once(__DIR__ . '/../config/session.php');
require_once(__DIR__ . '/../config/db.php');

// Initialize cart session
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// ADD TO CART
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $qty = max(1, intval($_POST['qty']));
    $stmt = $pdo->prepare('SELECT * FROM menu_items WHERE id = ?');
    $stmt->execute([$id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($item) {
        // if already in cart, add qty
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['qty'] += $qty;
        } else {
            $_SESSION['cart'][$id] = [
                'id' => $item['id'],
                'name' => $item['name'],
                'price' => $item['price'],
                'qty' => $qty,
                'branch_id' => $item['branch_id'] ?? null
            ];
        }
    }
    header('Location: cart.php');
    exit;
}

$cart = $_SESSION['cart'];

// REMOVE ITEM
if (isset($_GET['remove'])) {
    $remove_id = intval($_GET['remove']);
    unset($_SESSION['cart'][$remove_id]);
    header('Location: cart.php');
    exit;
}

// PLACE ORDER
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    if (empty($cart)) {
        $msg = 'Your cart is empty!';
    } else {
        require_login();

        $branch_id = intval($_POST['branch_id']);
        $payment_method = $_POST['payment_method'];

        // compute subtotal
        $subtotal = 0;
        foreach ($cart as $c) {
            $subtotal += $c['price'] * $c['qty'];
        }

        // delivery fee
        $delivery_fee = 0;
        $branch_name = '';
        switch ($branch_id) {
            case 1: $delivery_fee = 25; $branch_name = 'Rizal'; break;
            case 2: $delivery_fee = 35; $branch_name = 'Zulueta'; break;
            case 3: $delivery_fee = 35; $branch_name = 'Mabini'; break;
        }

        $total = $subtotal + $delivery_fee;

        // Get the delivery address from form input
$address = trim($_POST['address'] ?? '');

// insert into orders (now includes address)
$stmt = $pdo->prepare('INSERT INTO orders (user_id, branch_id, address, total, payment_method, status, created_at)
                       VALUES (?, ?, ?, ?, ?, "Pending", NOW())');
$stmt->execute([$_SESSION['user']['id'], $branch_id, $address, $total, $payment_method]);
$order_id = $pdo->lastInsertId();

// insert into order_items
$istmt = $pdo->prepare('INSERT INTO order_items (order_id, menu_item_id, qty, price)
                        VALUES (?, ?, ?, ?)');
foreach ($cart as $c) {
    $istmt->execute([$order_id, $c['id'], $c['qty'], $c['price']]);
}

unset($_SESSION['cart']);
$msg = "✅ Order placed successfully!<br>Order ID: <b>{$order_id}</b><br>Branch: <b>{$branch_name}</b><br>Delivery Fee: ₱{$delivery_fee}<br>Total: ₱" . number_format($total,2);

}
}

// AJAX add-to-cart handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    require_once(__DIR__ . '/../config/session.php');
    require_once(__DIR__ . '/../config/db.php');
    header('Content-Type: application/json');

    $id = intval($_POST['id']);
    $qty = max(1, intval($_POST['qty']));
    $stmt = $pdo->prepare('SELECT * FROM menu_items WHERE id = ?');
    $stmt->execute([$id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($item) {
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['qty'] += $qty;
        } else {
            $_SESSION['cart'][$id] = [
                'id' => $item['id'],
                'name' => $item['name'],
                'price' => $item['price'],
                'qty' => $qty,
                'branch_id' => $item['branch_id'] ?? null
            ];
        }

        echo json_encode(['success' => true, 'cart' => $_SESSION['cart']]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit;
}


?>


<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Cart</title>
    <link rel="stylesheet" href="/kamulan-system/assets/css/style.css">
    <style>
      body { background: #f5f5f5; font-family: Arial, sans-serif; }
      main.container { max-width: 900px; margin: 50px auto; background: white; padding: 20px; border-radius: 10px; }
      table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
      th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: center; }
      th { background: #ffa726; color: white; }
      .button { background: #ff7043; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; }
      .button:hover { background: #f4511e; }
      .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
      select, input[type="number"] { padding: 5px; border-radius: 5px; border: 1px solid #ccc; }
      .remove-link { color: red; text-decoration: none; font-size: 13px; }
      .nav-buttons { display: flex; justify-content: space-between; align-items: center; margin-top: 20px; }
    </style>
  </head>
  <body>
    <?php include(__DIR__ . '/../_partials/header.php'); ?>

<main class="container">
  <h2>Your Cart</h2>
  <?php if($msg) echo "<p class='success'>$msg</p>"; ?>

  <?php if (empty($cart)): ?>
      <p>Your cart is empty.</p>
      <a href="/kamulan-system/buyer/menu.php" class="button">Continue Ordering</a>
  <?php else: ?>
      <table>
        <tr>
          <th>Item</th>
          <th>Qty</th>
          <th>Price</th>
          <th>Subtotal</th>
          <th></th>
        </tr>
        <?php $subtotal = 0; ?>
        <?php foreach ($cart as $c): 
          $sub = $c['price'] * $c['qty'];
          $subtotal += $sub;
        ?>
        <tr>
          <td><?= htmlspecialchars($c['name']) ?></td>
          <td><?= intval($c['qty']) ?></td>
          <td>₱<?= number_format($c['price'],2) ?></td>
          <td>₱<?= number_format($sub,2) ?></td>
          <td><a class="remove-link" href="?remove=<?= $c['id'] ?>">Remove</a></td>
        </tr>
        <?php endforeach; ?>
      </table>

      <form method="post">
        <label><b>Choose Branch:</b></label>
        <select name="branch_id" required>
          <option value="">-- Select Branch --</option>
          <option value="1">Rizal</option>
          <option value="2">Zulueta</option>
          <option value="3">Mabini</option>
        </select>
        <br><br>

        <label><b>Payment Method:</b></label>
        <select name="payment_method" required>
          <option value="COD">Cash on Delivery</option>
          <option value="GCash">GCash</option>
        </select>
        <br><br>

        <!-- Ask for delivery address -->
<div style="margin-top: 15px;">
  <label for="address" style="font-weight:bold;">Delivery Address:</label><br>
  <input type="text" id="address" name="address" required 
         placeholder="Enter your delivery address"
         style="width:100%;padding:8px;border:1px solid #ccc;border-radius:6px;">
</div>


        <p><b>Subtotal:</b> ₱<?= number_format($subtotal,2) ?></p>
        <p><i>Delivery Fee depends on branch selected.</i></p>

        <div class="nav-buttons">
          <a href="/kamulan-system/buyer/menu.php" class="button">🛒 Continue Ordering</a>
          <button class="button" name="place_order" type="submit">Place Order</button>
        </div>
      </form>
  <?php endif; ?>
</main>

<?php include(__DIR__ . '/../_partials/footer.php'); ?>
</body>
</html>
