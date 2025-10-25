<?php
require_once(__DIR__ . '/../config/session.php');

$user = $_SESSION['user'] ?? null;
$userName = $user['name'] ?? '';
$role = $user['role'] ?? '';

// Redirect buyers or unauthenticated users away
if (!$user || $role === 'buyer') {
  header("Location: /kamulan-system/auth/login.php");
  exit;
}

// Determine correct dashboard path based on role
switch ($role) {
  case 'staff':
    $homeLink = "/kamulan-system/staff/dashboard.php";
    break;
  case 'rider':
    $homeLink = "/kamulan-system/rider/dashboard.php";
    break;
  case 'admin':
  case 'manager':
    $homeLink = "/kamulan-system/admin/dashboard.php";
    break;
  default:
    $homeLink = "/kamulan-system/index.php";
    break;
}
?>
<header class="site-header" style="
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: linear-gradient(90deg, #3e4425, #4b5320);
  padding: 12px 35px;
  color: white;
  font-family: 'Poppins', sans-serif;
  box-shadow: 0 2px 8px rgba(0,0,0,0.25);
  position: sticky;
  top: 0;
  z-index: 999;
">
  <!-- LOGO -->
  <div class="brand" style="display: flex; align-items: center; gap: 10px;">
    <img src="/kamulan-system/assets/images/kamulan-logo.jpg" alt="Kamulan Logo" style="height:45px; border-radius:8px;">
    <h1 style="margin: 0; font-size: 20px;">Kamulan Kitchenette</h1>
  </div>

  <!-- NAVIGATION -->
  <nav class="nav" style="display: flex; align-items: center; gap: 25px;">
    <a href="<?= $homeLink ?>" class="nav-link">Home</a>
    <a href="/kamulan-system/<?= $role ?>/profile.php" class="nav-link">Profile</a>
    <a href="/kamulan-system/auth/logout.php" class="nav-link logout" onclick="confirmLogout(event)">Logout</a>

    <span style="
      margin-left: 15px;
      background: #fff;
      color: #3e4425;
      padding: 6px 14px;
      border-radius: 25px;
      font-weight: bold;
      font-size: 14px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    ">
      <?= ucfirst(htmlspecialchars($role)) ?>: <?= htmlspecialchars($userName) ?>
    </span>
  </nav>

  <style>
    .nav-link {
      color: white;
      text-decoration: none;
      font-weight: 500;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 5px;
    }
    .nav-link:hover {
      color: #bcdeabff;
      transform: scale(1.05);
      text-shadow: 0 0 6px rgba(255,255,255,0.3);
    }
    .logout:hover {
      color: #ff6b6b;
      text-shadow: 0 0 6px rgba(255, 0, 0, 0.3);
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