<?php
require_once(__DIR__ . '/../config/session.php');
require_once(__DIR__ . '/../config/db.php');

// Fetch categories
$categories = $pdo->query("SELECT DISTINCT category FROM menu_items ORDER BY category ASC")->fetchAll(PDO::FETCH_COLUMN);
$category = $_GET['category'] ?? 'All';

if ($category !== 'All') {
    $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE category = ?");
    $stmt->execute([$category]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $items = $pdo->query("SELECT * FROM menu_items")->fetchAll(PDO::FETCH_ASSOC);
}

// Cart preview
$cart = $_SESSION['cart'] ?? [];
$total = 0;
foreach ($cart as $c) {
    $total += $c['price'] * $c['qty'];
}
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Kamulan - Menu</title>
    <link rel="stylesheet" href="/kamulan-system/assets/css/style.css">
    <style>
      body {
        background: #f5f5f5;
        font-family: "Poppins", sans-serif;
        margin: 0;
      }
      main.container {
        max-width: 1100px;
        margin: 30px auto;
        background: #fff;
        padding: 25px;
        border-radius: 12px;
      }
      h2 {
        color: #ff7043;
        margin-bottom: 20px;
      }
      .category-buttons {
        margin-bottom: 25px;
        text-align: center;
      }
      .category-buttons a {
        text-decoration: none;
        padding: 8px 15px;
        margin: 5px;
        border-radius: 20px;
        background: #ffa726;
        color: white;
        font-size: 14px;
        transition: 0.2s;
      }
      .category-buttons a:hover,
      .category-buttons a.active {
        background: #f57c00;
      }
      .search-box {
        text-align: center;
        margin-bottom: 20px;
      }
      .search-box input {
        padding: 8px 12px;
        width: 60%;
        border: 1px solid #ccc;
        border-radius: 8px;
      }
      .card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
        gap: 20px;
      }
      .card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        padding: 15px;
        text-align: center;
        transition: transform 0.2s;
      }
      .card:hover {
        transform: scale(1.02);
      }
      .card img {
        width: 100%;
        height: 160px;
        object-fit: cover;
        border-radius: 8px;
      }
      .card h4 {
        margin: 10px 0 5px;
        color: #333;
      }
      .card p {
        color: #666;
        font-size: 14px;
        height: 40px;
        overflow: hidden;
      }
      .price {
        color: #fff;
        background: #ff7043;
        padding: 5px 10px;
        border-radius: 5px;
        font-weight: bold;
        display: inline-block;
        margin-bottom: 10px;
      }
      .button {
        background: #ff7043;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 5px;
        cursor: pointer;
        transition: 0.2s;
      }
      .button:hover {
        background: #f4511e;
      }
      .not-available {
        background: #ccc;
        color: #666;
        padding: 8px 16px;
        border-radius: 5px;
        display: inline-block;
        font-weight: bold;
      }

      /* Floating Mini Cart */
      .floating-cart {
        position: fixed;
        right: 25px;
        bottom: 25px;
        background: #fff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        border-radius: 12px;
        padding: 15px;
        width: 260px;
        z-index: 1000;
        transition: all 0.3s ease;
      }
      .floating-cart h4 {
        margin-top: 0;
        color: #ff7043;
        text-align: center;
      }
      .floating-cart table {
        width: 100%;
        font-size: 13px;
      }
      .floating-cart td {
        padding: 4px;
      }
      .floating-cart .total {
        font-weight: bold;
        color: #333;
        border-top: 1px solid #ddd;
        padding-top: 8px;
        text-align: right;
      }
      .floating-cart .button {
        width: 100%;
        margin-top: 8px;
      }
    </style>
  </head>

  <body>
    <?php include(__DIR__ . '/../_partials/header.php'); ?>

    <main class="container">
      <h2>Our Menu</h2>

      <div class="category-buttons">
        <a href="?category=All" class="<?= $category==='All'?'active':'' ?>">All</a>
        <?php foreach ($categories as $cat): ?>
          <a href="?category=<?= urlencode($cat) ?>" class="<?= $category===$cat?'active':'' ?>">
            <?= htmlspecialchars($cat) ?>
          </a>
        <?php endforeach; ?>
      </div>

      <div class="search-box">
        <input type="text" id="searchInput" placeholder="Search for dishes...">
      </div>

      <div class="card-grid" id="menuGrid">
        <?php foreach($items as $it): ?>
          <div class="card" data-name="<?= strtolower($it['name']) ?>">
            <img src="/kamulan-system/assets/images/<?= htmlspecialchars($it['image']) ?>" alt="">
            <h4><?= htmlspecialchars($it['name']) ?></h4>
            <p><?= htmlspecialchars($it['description']) ?></p>
            <div class="price">₱<?= number_format($it['price'],2) ?></div>

            <?php if ($it['available'] == 0): ?>
              <div class="not-available">Not Available</div>
            <?php else: ?>
              <form class="addToCartForm">
                <input type="hidden" name="id" value="<?= $it['id'] ?>">
                <input type="number" name="qty" min="1" placeholder="0" style="width:60px">
                <button class="button" type="submit">Add to Cart</button>
              </form>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </main>

    <!-- Floating Mini Cart -->
    <div class="floating-cart" id="miniCart">
      <h4>Your Cart</h4>
      <div id="miniCartContent">
        <?php if (empty($cart)): ?>
          <p style="text-align:center;color:#666;">Cart is empty</p>
        <?php else: ?>
          <table>
            <?php foreach($cart as $c): ?>
              <tr>
                <td><?= htmlspecialchars($c['name']) ?></td>
                <td>x<?= $c['qty'] ?></td>
                <td style="text-align:right;">₱<?= number_format($c['price'] * $c['qty'], 2) ?></td>
              </tr>
            <?php endforeach; ?>
          </table>
          <div class="total">Total: ₱<?= number_format($total,2) ?></div>
          <a href="/kamulan-system/buyer/cart.php">
            <button class="button">Go to Checkout</button>
          </a>
        <?php endif; ?>
      </div>
    </div>

    <?php include(__DIR__ . '/../_partials/footer.php'); ?>

    <script>
      // Live search
      document.getElementById('searchInput').addEventListener('keyup', function() {
        let query = this.value.toLowerCase();
        document.querySelectorAll('#menuGrid .card').forEach(card => {
          let name = card.getAttribute('data-name');
          card.style.display = name.includes(query) ? 'block' : 'none';
        });
      });

      // AJAX Add to Cart
      document.querySelectorAll('.addToCartForm').forEach(form => {
        form.addEventListener('submit', function(e) {
          e.preventDefault();
          const formData = new FormData(this);

          fetch('/kamulan-system/buyer/cart_add.php', {
            method: 'POST',
            body: formData
          })
          .then(res => res.text())
          .then(html => {
            document.getElementById('miniCartContent').innerHTML = html;
          });
        });
      });
    </script>
  </body>
</html>
