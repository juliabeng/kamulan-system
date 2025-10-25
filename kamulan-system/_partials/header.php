<?php
require_once(__DIR__ . '/../config/session.php');

$user = $_SESSION['user'] ?? null;
$userName = $user['name'] ?? '';
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
    <a href="/kamulan-system/buyer/index.php" 
       style="color:white; text-decoration:none; font-weight:500; transition:0.3s;">Home</a>
    <a href="/kamulan-system/buyer/menu.php" 
       style="color:white; text-decoration:none; font-weight:500; transition:0.3s;">Menu</a>

    <?php if ($user): ?>
      <a href="/kamulan-system/buyer/cart.php" style="color:white; text-decoration:none; font-weight:500; transition:0.3s;">Cart</a>
      <a href="/kamulan-system/buyer/orders.php" style="color:white; text-decoration:none; font-weight:500; transition:0.3s;">My Orders</a>
      <a href="/kamulan-system/buyer/profile.php" style="color:white; text-decoration:none; font-weight:500; transition:0.3s;">Profile</a>
      <a href="/kamulan-system/auth/logout.php" class="nav-link logout" onclick="confirmLogout(event)" style="color:white; text-decoration:none; font-weight:500; transition:0.3s;">Logout</a>
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
      <a href="/kamulan-system/auth/login.php" style="color:white; text-decoration:none; font-weight:500; transition:0.3s;">Login</a>
    <?php endif; ?>
  </nav>

  <!-- HOVER EFFECTS -->
  <style>
    .site-header a:hover {
      color: #f4e04d; /* warm yellow accent */
      transform: scale(1.05);
      text-shadow: 0 0 6px rgba(255,255,255,0.3);
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
