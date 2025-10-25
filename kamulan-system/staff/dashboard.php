<?php
require_once(__DIR__ . '/../config/session.php');
require_once(__DIR__ . '/../config/db.php');
require_role(['staff']);
$user = $_SESSION['user'];
$branch_id = $user['branch_id'];
$stmt = $pdo->prepare('SELECT o.*, u.name as customer FROM orders o LEFT JOIN users u ON o.user_id=u.id WHERE o.branch_id = ? ORDER BY o.created_at DESC');
$stmt->execute([$branch_id]);
$orders = $stmt->fetchAll();
?>
<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Staff Dashboard</title>
    <link rel="stylesheet" href="/kamulan-system/assets/css/style.css">
  </head>
  <body>
    <?php include __DIR__ . '/../_partials/header_staff.php'; ?>

<main class="container">
  <h2>Orders for your branch</h2>
  <?php foreach($orders as $o): ?>
    <div class="card">
      <strong>Order #<?= $o['id'] ?></strong> - <?= htmlspecialchars($o['customer']) ?> - Status: <?= $o['status'] ?>
      <form method="post" action="update_status.php">
        <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
        <select name="status">
          <option>Preparing</option>
          <option>Out for Delivery</option>
          <option>Delivered</option>
        </select>
        <button class="button">Update</button>
      </form>
    </div>
  <?php endforeach; ?>
</main>

<?php include(__DIR__ . '/../_partials/footer.php'); ?>

</body>
</html>
