<?php
require_once(__DIR__ . '/../config/session.php');
require_once(__DIR__ . '/../config/db.php');

if (!isset($_SESSION['user'])) {
  header('Location: /kamulan-system/auth/login.php');
  exit;
}

$user_id = $_SESSION['user']['id'];

// fetch all orders of this user with branch name
$stmt = $pdo->prepare("
  SELECT o.*, b.name AS branch_name
  FROM orders o
  LEFT JOIN branches b ON o.branch_id = b.id
  WHERE o.user_id = ?
  ORDER BY o.created_at DESC
");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>My Orders - Kamulan</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
      .container { width: 90%; margin: 30px auto; }
      h2 { color: var(--dark-brown); margin-bottom: 20px; }
      table { width:100%; border-collapse:collapse; }
      th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
      th { background: #3b3b1f; color: #fff; }
      tr:hover { background: #f5f5f5; }
      .status {
        padding: 4px 8px; border-radius: 6px; font-weight: 600;
      }
      .pending { background:#ffeb99; color:#7a6600; }
      .preparing { background:#b3d9ff; color:#004080; }
      .on-delivery { background:#ffd480; color:#663300; }
      .completed { background:#c1f0c1; color:#006600; }
      .cancelled { background:#f0b3b3; color:#660000; }
      .empty { text-align:center; padding:40px; color:#666; }
      .details {
        margin-top:10px; background:#fafafa; padding:10px; border-radius:8px;
      }
    </style>
  </head>
<body>
<?php include __DIR__ . '/../_partials/header.php'; ?>

<div class="container">
  <h2>My Orders</h2>

  <?php if (empty($orders)): ?>
    <div class="empty">You have no orders yet.</div>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Order ID</th>
          <th>Branch</th>
          <th>Total</th>
          <th>Payment</th>
          <th>Status</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orders as $order): ?>
          <tr>
            <td>#<?= htmlspecialchars($order['id']) ?></td>
            <td><?= htmlspecialchars($order['branch_name'] ?? 'N/A') ?></td>
            <td>₱<?= number_format($order['total'], 2) ?></td>
            <td><?= htmlspecialchars(ucfirst($order['payment_method'])) ?></td>
            <td>
              <span class="status <?= strtolower($order['status']) ?>">
                <?= htmlspecialchars(ucfirst($order['status'])) ?>
              </span>
            </td>
            <td><?= date('M d, Y h:i A', strtotime($order['created_at'])) ?></td>
          </tr>
          <?php
          // fetch order items
          $itemStmt = $pdo->prepare("
            SELECT oi.*, m.name 
            FROM order_items oi 
            JOIN menu_items m ON oi.menu_item_id = m.id
            WHERE oi.order_id = ?
          ");
          $itemStmt->execute([$order['id']]);
          $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
          ?>
          <tr>
            <td colspan="6">
              <div class="details">
                <?php foreach ($items as $it): ?>
                  <div>
                    <?= htmlspecialchars($it['name']) ?> × <?= (int)$it['qty'] ?> — ₱<?= number_format($it['price'],2) ?>
                  </div>
                <?php endforeach; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/../_partials/footer.php'; ?>
</body>
</html>
