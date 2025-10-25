<?php
require_once(__DIR__ . '/config/db.php');

// Fetch Best Sellers (Top 3 most ordered items)
$stmt = $pdo->query("
  SELECT mi.*, COUNT(oi.menu_item_id) AS total_sold
  FROM order_items oi
  JOIN menu_items mi ON oi.menu_item_id = mi.id
  GROUP BY oi.menu_item_id
  ORDER BY total_sold DESC
  LIMIT 3
");
$bestSellers = $stmt->fetchAll();

// Fetch all categories dynamically
$categories = $pdo->query("SELECT DISTINCT category FROM menu_items ORDER BY category ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Kamulan Kitchenette</title>
  <link rel="stylesheet" href="/kamulan-system/assets/css/style.css">
  <link rel="stylesheet" href="/kamulan-system/assets/css/home.css">
  <style>
    .hidden { display: none; }
  </style>
</head>
<body>

<!-- HEADER -->
<header class="main-header" style="
  background: linear-gradient(90deg, #4b5d2a, #2f3e1b);
  color: #f5f5f5;
  padding: 15px 40px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 2px 6px rgba(0,0,0,0.3);
  position: sticky;
  top: 0;
  z-index: 999;
">
  <div class="logo" style="display:flex; align-items:center; gap:12px;">
    <img src="/kamulan-system/assets/images/kamulan-logo.jpg" alt="Kamulan Kitchenette Logo" style="height:50px; width:50px; border-radius:50%; object-fit:cover;">
    <h1 style="font-size:1.4rem; color:#fefae0; letter-spacing:1px; font-weight:600;">Kamulan Kitchenette</h1>
  </div>

  <nav class="nav-links" style="display:flex; gap:12px;">
    <a href="/kamulan-system/auth/register.php" class="btn" style="
      background:#7c8c56;
      color:#fff;
      padding:8px 16px;
      border-radius:6px;
      text-decoration:none;
      transition:all 0.3s ease;
      font-weight:500;
    " onmouseover="this.style.background='#a2b67e';" onmouseout="this.style.background='#7c8c56';">Register</a>

    <a href="/kamulan-system/auth/login.php" class="btn" style="
      background:#6b7b44;
      color:#fff;
      padding:8px 16px;
      border-radius:6px;
      text-decoration:none;
      transition:all 0.3s ease;
      font-weight:500;
    " onmouseover="this.style.background='#8da164';" onmouseout="this.style.background='#6b7b44';">Login</a>

    <a href="/kamulan-system/buyer/index.php" class="btn order-now" style="
      background:#a2b67e;
      color:#2f3e1b;
      padding:8px 18px;
      border-radius:6px;
      text-decoration:none;
      font-weight:600;
      transition:all 0.3s ease;
    " onmouseover="this.style.background='#cde0a8'; this.style.color='#1e2812';" onmouseout="this.style.background='#a2b67e'; this.style.color='#2f3e1b';">Order Now</a>
  </nav>
</header>

<!-- MAIN CONTENT -->
<main class="landing">

  <!-- TOP CONTROLS -->
  <section class="top-controls" style="display:flex; justify-content:space-between; align-items:center; gap:20px; flex-wrap:wrap; margin:20px;">
    <div class="control-buttons" style="display:flex; gap:10px;">
      <button id="menuBtn" class="tab-btn active">Menu</button>
      <button id="storesBtn" class="tab-btn">Branches</button>
    </div>

    <div class="search-bar" style="display:flex; align-items:center; gap:8px;">
      <input type="text" placeholder="Search menu..." style="padding:8px; border-radius:6px; border:1px solid #ccc;">
      <button style="padding:8px 12px; background:#6b7b44; color:white; border:none; border-radius:6px; cursor:pointer;">🔍</button>
    </div>
  </section>

  <!-- MENU SECTION -->
  <section id="menuSection" class="content-section">
    <!-- PHOTO CAROUSEL -->
    <div class="photo-carousel">
      <h2>Discover Our Dishes</h2>
      <div class="carousel-container">
        <div class="carousel-track">
          <img src="/kamulan-system/assets/images/kamulan1.jpg" alt="Gallery 1">
          <img src="/kamulan-system/assets/images/kamulan-cover.jpg" alt="Gallery 2">
          <img src="/kamulan-system/assets/images/chicken-fingers-platter.jpg" alt="Gallery 3">
          <img src="/kamulan-system/assets/images/quesadilla.jpg" alt="Gallery 4">
          <img src="/kamulan-system/assets/images/kamulan-drinks.jpg" alt="Gallery 5">
        </div>
      </div>
    </div>

    <!-- FEATURED -->
    <div class="featured-scroll">
      <h2>Featured Menu</h2>
      <div class="scroll-row">
        <?php foreach ($categories as $cat): ?>
          <div class="category-card" data-category="<?= htmlspecialchars($cat['category']) ?>">
            <img src="/kamulan-system/assets/images/<?= strtolower($cat['category']) ?>.jpg" 
                 alt="<?= htmlspecialchars($cat['category']) ?>" 
                 onerror="this.src='/kamulan-system/assets/images/kamulan-cover.jpg'">
            <h3><?= htmlspecialchars(ucfirst($cat['category'])) ?></h3>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- CATEGORY MENU DISPLAY -->
    <div id="category-items" class="category-items hidden">
      <div class="menu-grid">
        <?php if (!empty($items)): foreach ($items as $item): ?>
          <div class="menu-item">
            <h4><?= htmlspecialchars($item['name']) ?></h4>
            <span class="price">₱<?= number_format($item['price'], 2) ?></span>
          </div>
        <?php endforeach; endif; ?>
      </div>
    </div>

    <!-- BEST SELLERS -->
    <div class="bestsellers">
      <h2>Best Sellers</h2>
      <div class="meal-grid">
        <?php if ($bestSellers): ?>
          <?php foreach ($bestSellers as $meal): ?>
          <div class="meal-card">
            <img src="/kamulan-system/uploads/<?= htmlspecialchars($meal['image']) ?>" alt="<?= htmlspecialchars($meal['name']) ?>">
            <h3><?= htmlspecialchars($meal['name']) ?></h3>
            <p>₱<?= htmlspecialchars($meal['price']) ?> | <strong><?= $meal['total_sold'] ?></strong> sold</p>
          </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p>No best sellers yet.</p>
        <?php endif; ?>
      </div>
    </div>

    <!-- ABOUT -->
    <section style="display:flex; justify-content:center; align-items:center; flex-direction:column; padding:40px 20px; text-align:center;">
      <div class="about" style="max-width:700px;">
        <h2 style="color:#4b5d2a; margin-bottom:20px;">About Kamulan</h2>
        <img src="/kamulan-system/assets/images/kamulan-logo.jpg" 
             alt="Kamulan Logo" 
             style="width:120px; height:auto; display:block; margin:0 auto 20px auto; border-radius:12px;">
        <p style="font-size:1.05rem; line-height:1.6; color:#333;">
          Kamulan Kitchenette brings the rich flavors of Filipino home-style cooking to every table. 
          With our three branches across Laguna, we proudly serve dishes made with love and authenticity. 
          Experience Kamulan — where every meal feels like home.
        </p>
      </div>
    </section>
  </section>

  <!-- BRANCHES SECTION -->
  <section id="storesSection" class="content-section hidden">
    <h2>Our Branches</h2>
    <div class="branch-grid">
      <div class="branch-card">
        <img src="/kamulan-system/assets/images/branches/rizal.jpg" alt="Rizal">
        <h3>Kamulan – Rizal, Laguna</h3>
        <p>📍 Rizal, Laguna<br>☎ 0970-401-1952</p>
        <a href="/kamulan-system/auth/register.php" class="btn">Order Now</a>
      </div>
      <div class="branch-card">
        <img src="/kamulan-system/assets/images/branches/zulueta.jpg" alt="Zulueta">
        <h3>Kamulan – San Pablo City (P. Zulueta)</h3>
        <p>📍 P. Zulueta St., San Pablo City<br>☎ 0950-188-8568</p>
        <a href="/kamulan-system/auth/register.php" class="btn">Order Now</a>
      </div>
      <div class="branch-card">
        <img src="/kamulan-system/assets/images/branches/mabini.jpg" alt="Mabini">
        <h3>Kamulan – San Pablo City (A. Mabini)</h3>
        <p>📍 A. Mabini St., San Pablo City<br>☎ 0992-622-5396</p>
        <a href="/kamulan-system/auth/register.php" class="btn">Order Now</a>
      </div>
    </div>
  </section>

</main>

<?php include(__DIR__ . '/_partials/footer.php'); ?>

<!-- TOGGLE SCRIPT -->
<script>
const menuBtn = document.getElementById('menuBtn');
const storesBtn = document.getElementById('storesBtn');
const menuSec = document.getElementById('menuSection');
const storeSec = document.getElementById('storesSection');

menuBtn.addEventListener('click', () => {
  menuSec.classList.remove('hidden');
  storeSec.classList.add('hidden');
  menuBtn.classList.add('active');
  storesBtn.classList.remove('active');
});

storesBtn.addEventListener('click', () => {
  storeSec.classList.remove('hidden');
  menuSec.classList.add('hidden');
  storesBtn.classList.add('active');
  menuBtn.classList.remove('active');
});
</script>

<!-- CAROUSEL SCRIPT --> 
 <script> 
const container = document.querySelector('.carousel-container'); 
const btnLeft = document.querySelector('.carousel-btn.left'); 
const btnRight = document.querySelector('.carousel-btn.right'); 
const scrollStep = 320;

btnLeft.addEventListener('click', () => { container.scrollBy({ left: -scrollStep, behavior: 'smooth' }); 
});

btnRight.addEventListener('click', () => { container.scrollBy({ left: scrollStep, behavior: 'smooth' }); 
}); </script> 

<script>
// When category image is clicked 
document.querySelectorAll('.category-card').forEach(card => { 
  card.addEventListener('click', async () => { 
    const category = card.dataset.category;
    const itemsContainer = document.getElementById('category-items'); 
    
    // ✅ Fetch category items via AJAX (use backticks)
    const res = await fetch(`/kamulan-system/buyer/fetch_items.php?category=${encodeURIComponent(category)}`);
    const data = await res.text(); 
    
    // ✅ Insert HTML (use backticks for template string)
    itemsContainer.innerHTML = `
      <div class="cat-header">
        <h2>${category} Menu</h2>
        <button id="close-category">Close</button> 
      </div>
      ${data}
    `;
    
    itemsContainer.classList.remove('hidden'); 
    
    // ✅ Close button function
    document.getElementById('close-category').onclick = () => { 
      itemsContainer.classList.add('hidden'); 
      itemsContainer.innerHTML = ''; 
    }; 
  }); 
});
</script>



<script>
// Search functionality
document.querySelector('.search-bar button').addEventListener('click', function () {
  const query = document.querySelector('.search-bar input').value.toLowerCase();
  const items = document.querySelectorAll('.menu-item');
  items.forEach(item => {
    const name = item.querySelector('h4').textContent.toLowerCase();
    item.style.display = name.includes(query) ? 'block' : 'none';
  });
});

document.getElementById('menuBtn').addEventListener('click', () => { 
  const featured = document.querySelector('.featured-scroll'); 
  if (featured) featured.scrollIntoView({ behavior: 'smooth' }); 
  }); 
document.getElementById('storesBtn').addEventListener('click', () => { 
  const branches = document.querySelector('.branches'); 
  if (branches) branches.scrollIntoView({ behavior: 'smooth' });
  });
</script>

</body>
</html>
