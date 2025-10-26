<?php
require_once(__DIR__ . '/../config/session.php');
$user = $_SESSION['user'] ?? null;
$userName = $user['name'] ?? '';
$currentPage = basename($_SERVER['PHP_SELF']); // detect current page
?>

<header class="site-header" style="
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: linear-gradient(90deg, #4b5320, #3e4425);
  padding: 12px 35px;
  color: white;
  font-family: 'Poppins', sans-serif;
  box-shadow: 0 2px 8px rgba(0,0,0,0.25);
  position: sticky;
  top: 0;
  z-index: 999;
">
  <!-- BRAND -->
  <div class="brand" style="display: flex; align-items: center; gap: 10px;">
    <img src="/kamulan-system/assets/images/kamulan-logo.jpg" class="logo" alt="Kamulan Logo" style="height:45px; border-radius:8px;">
    <h1 style="margin: 0; font-size: 20px; letter-spacing: 0.5px;">Kamulan Kitchenette</h1>
  </div>

  <!-- NAVIGATION -->
  <nav class="nav" style="display: flex; align-items: center; gap: 22px;">
    <a href="/kamulan-system/buyer/index.php" class="<?= $currentPage === 'index.php' ? 'active-page' : '' ?>">Home</a>
    <a href="/kamulan-system/buyer/menu.php" class="<?= $currentPage === 'menu.php' ? 'active-page' : '' ?>">Menu</a>

    <?php if ($user): ?>
      <a href="/kamulan-system/buyer/cart.php" class="<?= $currentPage === 'cart.php' ? 'active-page' : '' ?>">Cart</a>
      <a href="/kamulan-system/buyer/orders.php" class="<?= $currentPage === 'orders.php' ? 'active-page' : '' ?>">My Orders</a>
      <a href="/kamulan-system/buyer/profile.php" class="<?= $currentPage === 'profile.php' ? 'active-page' : '' ?>">Profile</a>
      <a href="/kamulan-system/auth/logout.php" class="nav-link logout" onclick="confirmLogout(event)">Logout</a>
      <span style="
        margin-left:15px;
        background:#fff;
        color:#4b5320;
        padding:6px 14px;
        border-radius:25px;
        font-weight:bold;
        font-size:14px;
        box-shadow:0 2px 5px rgba(0,0,0,0.2);
      ">
        👋 Welcome, <?= htmlspecialchars($userName) ?>
      </span>
    <?php else: ?>
      <a href="/kamulan-system/auth/login.php">Login</a>
    <?php endif; ?>
  </nav>

  <style>
    .site-header a {
      color:white; 
      text-decoration:none; 
      font-weight:500; 
      transition:0.3s;
      position: relative;
    }
    .site-header a:hover {
      color: #000000;
      transform: scale(1.05);
       
    }
    /* ACTIVE PAGE STYLE */
    .site-header a.active-page {
      font-weight:700;
      color: #000000;
    
    }
  </style>
</header>

<script>
function confirmLogout(event) {
  event.preventDefault();
  if (confirm("Are you sure you want to logout?")) {
    window.location.href = "/kamulan-system/auth/logout.php";
  }
}
</script>

<div class="header-cart">
    🛒 Cart: <span id="cart-count"><?= count($_SESSION['cart'] ?? []) ?></span>
</div>

<style>
.header-cart {
    position: fixed;
    top: 20px;
    right: 20px;
    background: #ff7043;
    color: white;
    padding: 10px 15px;
    border-radius: 50px;
    font-weight: bold;
    cursor: pointer;
}
</style>
