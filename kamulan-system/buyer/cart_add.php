<?php
require_once(__DIR__ . '/../config/session.php');
require_once(__DIR__ . '/../config/db.php');

$id = $_POST['id'] ?? 0;
$qty = $_POST['qty'] ?? 0;

if ($id && $qty > 0) {
    $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE id = ?");
    $stmt->execute([$id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($item) {
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        $found = false;

        foreach ($_SESSION['cart'] as &$c) {
            if ($c['id'] == $id) {
                $c['qty'] += $qty;
                $found = true;
                break;
            }
        }
        unset($c);
        if (!$found) {
            $_SESSION['cart'][] = [
                'id' => $item['id'],
                'name' => $item['name'],
                'price' => $item['price'],
                'qty' => $qty
            ];
        }
    }
}

// Show updated mini cart
$cart = $_SESSION['cart'] ?? [];
$total = 0;
if (empty($cart)) {
    echo '<p style="text-align:center;color:#666;">Cart is empty</p>';
} else {
    echo '<table>';
    foreach ($cart as $c) {
        $total += $c['price'] * $c['qty'];
        echo '<tr>
                <td>'.htmlspecialchars($c['name']).'</td>
                <td>x'.$c['qty'].'</td>
                <td style="text-align:right;">₱'.number_format($c['price'] * $c['qty'], 2).'</td>
              </tr>';
    }
    echo '</table>
          <div class="total">Total: ₱'.number_format($total,2).'</div>
          <a href="/kamulan-system/buyer/cart.php">
            <button class="button">Go to Checkout</button>
          </a>';
}
?>
