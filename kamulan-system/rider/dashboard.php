<?php
require_once(__DIR__ . '/../config/session.php');
require_once(__DIR__ . '/../config/db.php');
require_role(['rider']);

$user = $_SESSION['user'];

// Determine branch identifier (either ID or name)
$branch_id = $user['branch_id'] ?? null;
$branch_name = $user['branch_name'] ?? null;

// Prepare query depending on what’s available
if ($branch_id) {
    $stmt = $pdo->prepare('
        SELECT o.*, u.name AS customer 
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        WHERE o.branch_id = ? 
          AND o.status = "Placed"
        ORDER BY o.created_at DESC
    ');
    $stmt->execute([$branch_id]);
} elseif ($branch_name) {
    $stmt = $pdo->prepare('
        SELECT o.*, u.name AS customer 
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        LEFT JOIN branches b ON o.branch_id = b.id 
        WHERE b.name = ? 
          AND o.status = "Placed"
        ORDER BY o.created_at DESC
    ');
    $stmt->execute([$branch_name]);
} else {
    $orders = [];
}

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Rider Dashboard - Kamulan</title>
  <link rel="stylesheet" href="/kamulan-system/assets/css/style.css">
</head>
<body>

<?php include(__DIR__ . '/../_partials/header_staff.php'); ?>

<main class="container">
  <h2>Available Deliveries (<?php echo htmlspecialchars($user['branch_name'] ?? 'Unknown Branch'); ?>)</h2>

  <?php if (empty($orders)): ?>
    <p>No available deliveries for your branch.</p>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Order ID</th>
          <th>Customer</th>
          <th>Total</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orders as $o): ?>
        <tr>
          <td data-label="Order ID">#<?php echo $o['id']; ?></td>
          <td data-label="Customer"><?php echo htmlspecialchars($o['customer']); ?></td>
          <td data-label="Total">₱<?php echo number_format($o['total'], 2); ?></td>
          <td data-label="Action">
            <a href="active_delivery.php?id=<?php echo $o['id']; ?>">View</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</main>

<?php include(__DIR__ . '/../_partials/footer.php'); ?>

</body>
</html>
