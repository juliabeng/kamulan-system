<?php
require_once(__DIR__ . '/../config/session.php');
require_once(__DIR__ . '/../config/db.php');
require_role(['manager']);

$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

$query = "
  SELECT o.id, u.name AS customer, b.name AS branch, o.total, o.payment_method, o.status, o.created_at
  FROM orders o
  LEFT JOIN users u ON o.user_id = u.id
  LEFT JOIN branches b ON o.branch_id = b.id
  WHERE 1
";

$params = [];
if ($from && $to) {
    $query .= " AND DATE(o.created_at) BETWEEN ? AND ?";
    $params = [$from, $to];
} elseif ($from) {
    $query .= " AND DATE(o.created_at) >= ?";
    $params = [$from];
} elseif ($to) {
    $query .= " AND DATE(o.created_at) <= ?";
    $params = [$to];
}

$query .= " ORDER BY o.created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Sales & Orders Report - Kamulan Kitchenette</title>
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
    h2 { color: #4a3b20; margin-bottom: 15px; }
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
    tr:hover { background: #f9f5ef; }
    .print-btn {
      background: #6b4f2c;
      color: white;
      border: none;
      border-radius: 6px;
      padding: 8px 16px;
      cursor: pointer;
      float: right;
      margin-bottom: 10px;
    }
    .print-btn:hover { background: #4a3b20; }
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

    .back-btn {
      text-decoration: none;
      color: #6b4f2c;
      font-size: 16px;
      display: inline-flex;
      align-items: center;
      margin-bottom: 15px;
    }
    .back-btn svg {
      width: 22px;
      height: 22px;
      margin-right: 6px;
      stroke-width: 2;
    }

    @media print {
      form, .back-btn, .print-btn, header, nav, footer { display: none !important; }
      body { background: #fff; }
      table, th, td { border: 1px solid #000; }
      h2 { text-align: center; color: #000; }
    }
  </style>
</head>
<body>
<?php include __DIR__ . '/../_partials/header_staff.php'; ?>

<div class="container">

  <a href="../admin/dashboard.php" class="back-btn">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
    </svg>
    Back
  </a>

  <h2>Sales & Orders Report</h2>

  <form method="get">
    <label>From:</label>
    <input type="date" name="from" value="<?= htmlspecialchars($from) ?>">
    <label>To:</label>
    <input type="date" name="to" value="<?= htmlspecialchars($to) ?>">
    <button type="submit">Filter</button>
    <button type="button" class="print-btn" onclick="window.print()">🖨️ Print Report</button>
  </form>

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
      <tr><td colspan="7">No orders found for selected range.</td></tr>
    <?php endif; ?>
  </table>

</div>
</body>
</html>
