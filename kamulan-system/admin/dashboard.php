<?php
require_once(__DIR__ . '/../config/session.php');
require_once(__DIR__ . '/../config/db.php');
require_role(['manager']);

// --- Fetch quick stats ---
$today_sales = $pdo->query("
    SELECT COALESCE(SUM(total), 0)
    FROM orders
    WHERE DATE(created_at) = CURDATE()
")->fetchColumn();

$month_sales = $pdo->query("
    SELECT COALESCE(SUM(total), 0)
    FROM orders
    WHERE MONTH(created_at) = MONTH(CURDATE())
      AND YEAR(created_at) = YEAR(CURDATE())
")->fetchColumn();

$active_orders = $pdo->query("
    SELECT COUNT(*)
    FROM orders
    WHERE status IN ('Placed','Preparing','Out for Delivery')
")->fetchColumn();

// --- Handle date filter ---
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

$query = "
    SELECT 
        o.id,
        COALESCE(u.name, 'Guest') AS customer,
        COALESCE(b.name, 'Unknown Branch') AS branch,
        o.total,
        o.payment_method,
        o.status,
        o.created_at
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    LEFT JOIN branches b ON o.branch_id = b.id
    WHERE 1
";

$params = [];

if (!empty($from) && !empty($to)) {
    $query .= " AND DATE(o.created_at) BETWEEN ? AND ?";
    $params = [$from, $to];
} elseif (!empty($from)) {
    $query .= " AND DATE(o.created_at) >= ?";
    $params = [$from];
} elseif (!empty($to)) {
    $query .= " AND DATE(o.created_at) <= ?";
    $params = [$to];
}

$query .= " ORDER BY o.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$bestSellerQuery = "
    SELECT 
        m.name AS item_name,
        SUM(oi.qty) AS total_sold,
        SUM(oi.qty * oi.price) AS total_revenue
    FROM order_items oi
    INNER JOIN orders o ON oi.order_id = o.id
    INNER JOIN menu_items m ON oi.menu_item_id = m.id
    WHERE o.status IN ('Completed', 'Delivered')
    GROUP BY m.id
    ORDER BY total_sold DESC
    LIMIT 10
";
$bestSellers = $pdo->query($bestSellerQuery)->fetchAll(PDO::FETCH_ASSOC);


// --- Get current month and year dynamically ---
$currentMonth = date('F'); // e.g. October
$currentYear = date('Y');  // e.g. 2025

// --- Query only orders from the current month ---
$query = "
  SELECT o.id, u.name AS customer, b.name AS branch, o.total, o.payment_method, o.status, o.created_at
  FROM orders o
  LEFT JOIN users u ON o.user_id = u.id
  LEFT JOIN branches b ON o.branch_id = b.id
  WHERE MONTH(o.created_at) = MONTH(CURDATE()) 
    AND YEAR(o.created_at) = YEAR(CURDATE())
  ORDER BY o.created_at DESC
";
$stmt = $pdo->query($query);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>



<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Manager Dashboard - Kamulan Kitchenette</title>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f6f4f0;
      margin: 0;
    }
    .container {
      width: 90%;
      margin: 30px auto;
      background: #fff;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    h2 { color: #4a3b20; }
    .stats {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      margin-bottom: 25px;
    }
    .card {
      flex: 1 1 200px;
      background: #ede3d2;
      padding: 15px;
      border-radius: 10px;
      text-align: center;
      box-shadow: inset 0 0 3px rgba(0,0,0,0.1);
    }
    .card h3 {
      margin: 5px 0;
      color: #3b2c16;
    }
    .card p {
      font-size: 20px;
      font-weight: 600;
      color: #6b4f2c;
    }
    .links {
      margin-bottom: 20px;
      text-align: center;
    }
    .links a {
      background: #6b4f2c;
      color: white;
      padding: 10px 18px;
      border-radius: 8px;
      text-decoration: none;
      margin: 5px;
      display: inline-block;
      transition: background 0.2s;
    }
    .links a:hover { background: #4a3b20; }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }
    th, td {
      border-bottom: 1px solid #ddd;
      padding: 10px;
      text-align: center;
    }
    th {
      background: #6b4f2c;
      color: white;
    }
    tr:hover {
      background: #f9f5ef;
    }
    form {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      align-items: center;
      margin-bottom: 10px;
    }
    input[type="date"] {
      padding: 5px 8px;
      border-radius: 6px;
      border: 1px solid #bbb;
    }
    button {
      padding: 6px 14px;
      background: #6b4f2c;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }
    button:hover { background: #4a3b20; }
  </style>
</head>
<body>
<?php include __DIR__ . '/../_partials/header_staff.php'; ?>

<div class="container">
  <h2>Manager Dashboard</h2>

  <div class="stats">
    <div class="card">
      <h3>Today's Sales</h3>
      <p>₱<?= number_format($today_sales, 2) ?></p>
    </div>
    <div class="card">
      <h3>Monthly Sales</h3>
      <p>₱<?= number_format($month_sales, 2) ?></p>
    </div>
    <div class="card">
      <h3>Active Orders</h3>
      <p><?= $active_orders ?></p>
    </div>
  </div>

  <div class="links">
    <a href="/kamulan-system/admin/manage_menu.php">Manage Menu</a>
    <a href="/kamulan-system/admin/manage_branches.php">Manage Branches</a>
    <a href="/kamulan-system/admin/manage_users.php">Manage Users</a>
  </div>

<h3>Top 10 Best Seller Foods</h3>

<!-- Button to view full sales -->
<div style="text-align:right; margin-bottom:10px;">
  <a href="total_sales.php"
  style="background:#6b4f2c; color:white; text-decoration:none; padding:8px 14px; border-radius:6px;">
     View Total Sales of All Foods
  </a>
</div>

<table border="1" cellspacing="0" cellpadding="8" style="width:100%; border-collapse:collapse;">
  <thead>
    <tr style="background:#4b5320; color:white;">
      <th style="width:10%;">Rank</th>
      <th>Food Name</th>
      <th style="width:20%;">Total Sold</th>
    </tr>
  </thead>
  <tbody>
    <?php if (!empty($bestSellers)): ?>
      <?php foreach ($bestSellers as $index => $item): ?>
        <tr style="text-align:center;">
          <td><?= $index + 1 ?></td>
          <td><?= htmlspecialchars($item['item_name']) ?></td>
          <td><?= htmlspecialchars($item['total_sold']) ?></td>
        </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr><td colspan="3" style="text-align:center;">No data available</td></tr>
    <?php endif; ?>
  </tbody>
</table>



<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
  <h3>Sales & Orders Report (<?= $currentMonth . ' ' . $currentYear ?>)</h3>
  <a href="sales_report.php" 
     style="background:#6b4f2c; color:white; text-decoration:none; padding:8px 14px; border-radius:6px;">
     📄 View Full Report
  </a>
</div>

<table>
  <tr>
    <th>Order ID</th>
    <th>Customer</th>
    <th>Branch</th>
    <th>Total (₱)</th>
    <th>Payment</th>
    <th>Status</th>
    <th>Date</th>
  </tr>
  <?php if (count($orders) > 0): ?>
    <?php foreach ($orders as $o): ?>
      <tr>
        <td><?= $o['id'] ?></td>
        <td><?= htmlspecialchars($o['customer'] ?? 'Guest') ?></td>
        <td><?= htmlspecialchars($o['branch'] ?? '—') ?></td>
        <td><?= number_format($o['total'], 2) ?></td>
        <td><?= htmlspecialchars($o['payment_method']) ?></td>
        <td><?= htmlspecialchars($o['status']) ?></td>
        <td><?= date('Y-m-d H:i', strtotime($o['created_at'])) ?></td>
      </tr>
    <?php endforeach; ?>
  <?php else: ?>
    <tr><td colspan="7">No orders found for <?= $currentMonth ?>.</td></tr>
  <?php endif; ?>
</table>
</div>

</body>
</html>
