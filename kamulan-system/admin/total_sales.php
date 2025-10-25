<?php
require_once(__DIR__ . '/../config/session.php');
require_once(__DIR__ . '/../config/db.php');
require_role(['manager']);

// Fetch all foods total sales
$query = "
    SELECT 
        m.name AS item_name,
        SUM(oi.qty) AS total_sold,
        SUM(oi.qty * oi.price) AS total_sales
    FROM order_items oi
    INNER JOIN orders o ON oi.order_id = o.id
    INNER JOIN menu_items m ON oi.menu_item_id = m.id
    WHERE o.status IN ('Completed', 'Delivered')
    GROUP BY m.id
    ORDER BY total_sold DESC
";
$stmt = $pdo->query($query);
$allFoods = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Total Sales - Kamulan Kitchenette</title>
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
    h2 { color: #4a3b20; margin-bottom: 10px; }
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
    .no-data {
      text-align: center;
      padding: 20px;
      color: #666;
    }
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
      .back-btn, .print-btn, header {
        display: none !important;
      }
      .container {
        box-shadow: none;
      }
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

  <button class="print-btn" onclick="window.print()">🖨️ Print Report</button>

  <h2>Total Sales of All Foods</h2>

  <table>
    <thead>
      <tr>
        <th style="width:10%;">Rank</th>
        <th>Food Name</th>
        <th style="width:15%;">Total Sold</th>
        <th style="width:20%;">Total Sales (₱)</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($allFoods)): ?>
        <?php foreach ($allFoods as $index => $food): ?>
          <tr>
            <td><?= $index + 1 ?></td>
            <td><?= htmlspecialchars($food['item_name']) ?></td>
            <td><?= htmlspecialchars($food['total_sold']) ?></td>
            <td>₱<?= number_format($food['total_sales'], 2) ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="4" class="no-data">No sales data available</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

</body>
</html>
