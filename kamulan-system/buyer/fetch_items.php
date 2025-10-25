<?php
require_once(__DIR__ . '/../config/db.php');

$category = $_GET['category'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM menu_items WHERE category = ?");
$stmt->execute([$category]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$items) {
  echo "<p style='text-align:center; color:#555;'>No items found in this category.</p>";
  exit;
}
?>

<div class="menu-grid">
  <?php foreach ($items as $item): ?>
    <div class="menu-card">
      <h4><?= htmlspecialchars($item['name']) ?></h4>
      <span class="price">₱<?= number_format($item['price'], 2) ?></span>
    </div>
  <?php endforeach; ?>
</div>