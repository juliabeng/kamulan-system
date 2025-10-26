<?php
require_once(__DIR__ . '/../config/session.php');
require_once(__DIR__ . '/../config/db.php');

// Logged-in user info
$user = $_SESSION['user'] ?? null;
$userName = $user['name'] ?? '';

// Detect current page filename for active nav
$currentPage = basename($_SERVER['PHP_SELF']);

// Fetch categories
$categories = $pdo->query("SELECT DISTINCT category FROM menu_items ORDER BY category ASC")->fetchAll(PDO::FETCH_COLUMN);

// Fetch branches
$branches = $pdo->query("SELECT * FROM branches")->fetchAll(PDO::FETCH_ASSOC);

// Handle search
$searchQuery = trim($_GET['q'] ?? '');
$searchResults = [];
if ($searchQuery !== '') {
    $sstmt = $pdo->prepare("SELECT * FROM menu_items WHERE (name LIKE ? OR description LIKE ?) AND available > 0 ORDER BY created_at DESC");
    $sstmt->execute(["%$searchQuery%", "%$searchQuery%"]);
    $searchResults = $sstmt->fetchAll(PDO::FETCH_ASSOC);
}

// Preload all items per category
$all_by_category = [];
foreach ($categories as $cat) {
    $stmtAll = $pdo->prepare("SELECT * FROM menu_items WHERE category = ? AND available > 0 ORDER BY created_at DESC");
    $stmtAll->execute([$cat]);
    $all_by_category[$cat] = $stmtAll->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Kamulan - Home</title>
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="../assets/css/home.css">
<style>
/* --- General Styles --- */
.container { width: 90%; margin: 30px auto; }
.welcome { text-align:left; margin-bottom:14px; color: var(--dark-brown); font-weight:600; }
.search-bar { text-align:center; margin-bottom:24px; }
.search-bar input { padding:10px; width:320px; border-radius:8px; border:1px solid #ccc; }
.search-bar button { padding:10px 14px; margin-left:8px; border-radius:8px; border:none; background:var(--accent); color:var(--text-light); cursor:pointer; }

.category-section { margin-bottom:50px; }
.category-section h2 { margin-bottom:12px; }
.card-grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(220px,1fr)); gap:20px; }
.card { background:#fff; border-radius:12px; box-shadow:var(--shadow); padding:12px; display:flex; flex-direction:column; justify-content:space-between; cursor:pointer; transition: transform .15s ease; height:340px; }
.card:hover { transform: translateY(-6px); }
.card img { width:100%; height:150px; object-fit:cover; border-radius:8px; margin-bottom:10px; }
.card h4 { margin:6px 0 8px; color:var(--primary); font-size:1.05rem; }
.card p { color:#555; font-size:0.9rem; flex-grow:1; overflow:hidden; }
.card .price { background: #556B2F; color:var(--text-light); padding:8px 10px; border-radius:8px; display:inline-block; font-weight:700; margin-top:10px; }

.view-all { text-align:center; margin-top:12px; }
.view-all button { background: var(--primary); color: var(--text-light); border:none; padding:8px 14px; border-radius:8px; cursor:pointer; }

.hidden-item { display:none; }

/* Branches */
.branches { margin-top:30px; background-color:#556B2F; border-radius:18px; padding:30px 20px; text-align:center; color:#f5f5f5; }
.branches h2 { font-size:2rem; margin-bottom:30px; letter-spacing:1px; color:#ffffff; }
.branch-grid { display:flex; justify-content:center; gap:20px; overflow-x:auto; padding-bottom:10px; }
.branch-card { flex:0 0 250px; background:#fff; color:#333; border-radius:12px; box-shadow:0 4px 10px rgba(0,0,0,0.2); padding:15px; text-align:center; }
.branch-card img { width:100%; height:140px; object-fit:cover; border-radius:8px; margin-bottom:10px; }
.branch-card h3 { color:#556B2F; margin:6px 0; font-size:1.05rem; }
.branch-card p { font-size:0.85rem; }

@media (max-width:720px) {
  .card { height:auto; }
  .card img { height:140px; }
}
</style>
</head>
<body>

<?php include __DIR__ . '/../_partials/header.php'; ?>

<main class="container">
  <div class="welcome">Welcome, <?= htmlspecialchars($userName ?: 'Guest') ?> ✨</div>

  <div class="search-bar">
    <form method="GET" action="">
      <input type="text" name="q" placeholder="Search food (name / description)..." value="<?= htmlspecialchars($searchQuery) ?>">
      <button type="submit">Search</button>
    </form>
  </div>

  <!-- SEARCH RESULTS -->
  <?php if ($searchQuery !== ''): ?>
  <div class="search-results">
    <h2>Search Results for "<?= htmlspecialchars($searchQuery) ?>"</h2>
    <?php if (empty($searchResults)): ?>
      <p style="text-align:center;color:#666;">No results found.</p>
    <?php else: ?>
      <div class="card-grid">
        <?php foreach($searchResults as $f): ?>
        <div class="card" onclick="location.href='menu.php?id=<?= (int)$f['id'] ?>'">
          <img src="/kamulan-system/assets/images/<?= htmlspecialchars($f['image'] ?: 'default.jpg') ?>" alt="">
          <h4><?= htmlspecialchars($f['name']) ?></h4>
          <p><?= htmlspecialchars($f['description']) ?></p>
          <div class="price">₱<?= number_format($f['price'],2) ?></div>
        </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  <!-- CATEGORY SECTIONS -->
  <?php foreach ($categories as $category):
      $items = $all_by_category[$category] ?? [];
      if (empty($items)) continue;
  ?>
  <div class="category-section">
    <h2><?= htmlspecialchars($category) ?></h2>
    <div class="card-grid">
      <?php foreach ($items as $i => $f): 
          $hideClass = ($i >= 3) ? 'hidden-item' : '';
      ?>
        <div class="card <?= $hideClass ?>" data-index="<?= $i ?>" onclick="location.href='menu.php?id=<?= (int)$f['id'] ?>'">
          <img src="/kamulan-system/assets/images/<?= htmlspecialchars($f['image'] ?: 'default.jpg') ?>" alt="<?= htmlspecialchars($f['name']) ?>">
          <h4><?= htmlspecialchars($f['name']) ?></h4>
          <p><?= htmlspecialchars($f['description']) ?></p>
          <div class="price">₱<?= number_format($f['price'],2) ?></div>
        </div>
      <?php endforeach; ?>
    </div>

    <?php if (count($items) > 3): ?>
    <div class="view-all">
      <button type="button" onclick="toggleViewAll(this)">View All</button>
    </div>
    <?php endif; ?>
  </div>
  <?php endforeach; ?>

  <!-- BRANCHES SECTION -->
  <div class="branches">
    <h2>Our Branches</h2>
    <div class="branch-grid">
      <?php foreach ($branches as $b): ?>
        <div class="branch-card">
          <img src="/kamulan-system/assets/images/branches/<?= htmlspecialchars($b['image'] ?? 'default.jpg') ?>" alt="<?= htmlspecialchars($b['name']) ?>">
          <h3><?= htmlspecialchars($b['name']) ?></h3>
          <p>📍 <?= htmlspecialchars($b['address'] ?? 'Address not set') ?><br>☎ <?= htmlspecialchars($b['contact'] ?? 'N/A') ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

</main>

<?php include(__DIR__ . '/../_partials/footer.php'); ?>

<script>
/* Toggle View All / Show Less */
function toggleViewAll(btn) {
  const section = btn.closest('.category-section');
  const hiddenCards = section.querySelectorAll('.card.hidden-item, .card:not(.hidden-item)');

  const isCollapsed = Array.from(hiddenCards).some(c => c.classList.contains('hidden-item'));

  hiddenCards.forEach(c => {
    if (isCollapsed) {
      c.classList.remove('hidden-item');
    } else if (c.dataset.index >= 3) {
      c.classList.add('hidden-item');
    }
  });

  btn.textContent = isCollapsed ? 'Show Less' : 'View All';
}
</script>

</body>
</html>
