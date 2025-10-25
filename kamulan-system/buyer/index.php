<?php
require_once(__DIR__ . '/../config/session.php');
require_once(__DIR__ . '/../config/db.php');

// fetch logged-in user name for welcome
$user_name = $_SESSION['user']['name'] ?? 'Guest';

// Fetch categories
$categories = $pdo->query("SELECT DISTINCT category FROM menu_items ORDER BY category ASC")->fetchAll(PDO::FETCH_COLUMN);

// Fetch branches
$branches = $pdo->query("SELECT * FROM branches")->fetchAll(PDO::FETCH_ASSOC);

// Handle search (runs on this page)
$searchQuery = trim($_GET['q'] ?? '');
$searchResults = [];
if ($searchQuery !== '') {
    $sstmt = $pdo->prepare("SELECT * FROM menu_items WHERE (name LIKE ? OR description LIKE ?) AND available > 0 ORDER BY created_at DESC");
    $sstmt->execute(["%$searchQuery%", "%$searchQuery%"]);
    $searchResults = $sstmt->fetchAll(PDO::FETCH_ASSOC);
}

// Preload first 3 items and all items per category (used by View All)
$first3 = [];
$all_by_category = [];
foreach ($categories as $cat) {
    $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE category = ? AND available > 0 ORDER BY created_at DESC LIMIT 3");
    $stmt->execute([$cat]);
    $first3[$cat] = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
      /* page-specific tweaks */
      .container { width: 90%; margin: 30px auto; }
      .welcome { text-align: left; margin-bottom: 14px; color: var(--dark-brown); font-weight: 600; }
      .search-bar { text-align: center; margin-bottom: 24px; }
      .search-bar input {
        padding: 10px; width: 320px; border-radius: 8px; border: 1px solid #ccc;
      }
      .search-bar button { padding: 10px 14px; margin-left:8px; border-radius:8px; border:none; background:var(--accent); color:var(--text-light); cursor:pointer; }
      .category-section { margin-bottom: 50px; }
      .category-section h2 { margin-bottom: 12px; }
      .card-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px,1fr)); gap: 20px; align-items:stretch; }
      .card {
        background:#fff; border-radius:12px; box-shadow:var(--shadow); padding:12px; width:100%;
        display:flex; flex-direction:column; justify-content:space-between; height:340px; cursor:pointer; transition: transform .15s ease;
      }
      .card:hover { transform: translateY(-6px); }
      .card img { width:100%; height:150px; object-fit:cover; border-radius:8px; margin-bottom:10px; }
      .card h4 { margin:6px 0 8px; color:var(--primary); font-size:1.05rem; }
      .card p { color: #555; font-size:0.9rem; flex-grow:1; overflow:hidden; }
      .card .price {
        background: var(--accent); color: var(--text-light); padding: 8px 10px; border-radius:8px; display:inline-block; font-weight:700; margin-top:10px;
      }

      .view-all { text-align:center; margin-top: 12px; }
      .view-all button { background: var(--primary); color: var(--text-light); border: none; padding: 8px 14px; border-radius: 8px; cursor:pointer; }

      /* full-category panel slide animation using max-height */
      .full-panel {
        margin-top: 14px; padding: 16px; border-radius: 12px; background: #fff; box-shadow: var(--shadow);
        overflow: hidden; max-height: 0; transition: max-height 0.45s cubic-bezier(.2,.9,.2,1);
      }
      .full-panel.open { max-height: 2000px; } /* large enough to show content */
      .panel-header { display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:12px; }
      .panel-header h3 { margin:0; color:var(--primary); }
      .panel-header button { background: var(--dark-brown); color: var(--text-light); border: none; padding: 6px 12px; border-radius: 8px; cursor:pointer; }

      /* Search results area */
      .search-results { margin-bottom: 30px; }
      .search-results h2 { margin-bottom: 12px; }

      .branches { margin-top: 30px; }
      .branch-grid { display:flex; flex-wrap:wrap; justify-content:center; gap:20px; }
      .branch-card { width: 280px; background:#fff; padding:12px; border-radius:12px; box-shadow:var(--shadow); text-align:center; }
      .branch-card img { width:100%; height:160px; object-fit:cover; border-radius:8px; margin-bottom:10px; }
      .branch-card h3 { margin:6px 0; color:var(--primary); }
      .branch-card p { color:#555; font-size:14px; }

      @media (max-width:720px) {
        .card { height:auto; }
        .card img { height:140px; }
      }
    </style>
  </head>
<body>
<?php include __DIR__ . '/../_partials/header.php'; ?>

<main class="container">

  <!-- Welcome -->
  <div class="welcome">Welcome, <?= htmlspecialchars($user_name) ?> ✨</div>

  <!-- SEARCH BAR (submits to this page) -->
  <div class="search-bar">
    <form method="GET" action="">
      <input type="text" name="q" placeholder="Search food (name / description)..." value="<?= htmlspecialchars($searchQuery) ?>">
      <button type="submit">Search</button>
    </form>
  </div>

  <!-- SEARCH RESULTS (if search present) -->
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

  <!-- CATEGORY SECTIONS (show first 3 each) -->
  <?php foreach ($categories as $category):
      $items = $first3[$category] ?? [];
      if (empty($items)) continue;
      $panelId = 'panel-' . htmlspecialchars(preg_replace('/\s+/', '-', strtolower($category)));
  ?>
    <div class="category-section" id="cat-<?= htmlspecialchars(preg_replace('/\s+/', '-', strtolower($category))) ?>">
      <h2><?= htmlspecialchars($category) ?></h2>

      <div class="card-grid">
        <?php foreach ($items as $f): ?>
          <div class="card" onclick="location.href='menu.php?id=<?= (int)$f['id'] ?>'">
            <img src="/kamulan-system/assets/images/<?= htmlspecialchars($f['image'] ?: 'default.jpg') ?>" alt="<?= htmlspecialchars($f['name']) ?>">
            <h4><?= htmlspecialchars($f['name']) ?></h4>
            <p><?= htmlspecialchars($f['description']) ?></p>
            <div class="price">₱<?= number_format($f['price'], 2) ?></div>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="view-all">
        <button type="button" onclick="togglePanel('<?= addslashes($panelId) ?>')">View All</button>
      </div>

      <!-- Full category panel initially closed -->
      <div class="full-panel" id="<?= $panelId ?>">
        <div class="panel-header">
          <h3>All in <?= htmlspecialchars($category) ?></h3>
          <div>
            <button type="button" onclick="togglePanel('<?= addslashes($panelId) ?>')">Close</button>
          </div>
        </div>

        <div class="card-grid">
          <?php foreach ($all_by_category[$category] as $f): ?>
            <div class="card" onclick="location.href='menu.php?id=<?= (int)$f['id'] ?>'">
              <img src="/kamulan-system/assets/images/<?= htmlspecialchars($f['image'] ?: 'default.jpg') ?>" alt="<?= htmlspecialchars($f['name']) ?>">
              <h4><?= htmlspecialchars($f['name']) ?></h4>
              <p><?= htmlspecialchars($f['description']) ?></p>
              <div class="price">₱<?= number_format($f['price'], 2) ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
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
/* toggles the full-panel with a slide animation using max-height transition */
function togglePanel(panelId) {
  const el = document.getElementById(panelId);
  if (!el) return;
  const isOpen = el.classList.contains('open');
  if (isOpen) {
    el.classList.remove('open');
    // optional: scroll back to category title
    const catId = panelId.replace('panel-', 'cat-');
    const catEl = document.getElementById(catId);
    if (catEl) catEl.scrollIntoView({behavior: 'smooth', block: 'start'});
  } else {
    // close other panels
    document.querySelectorAll('.full-panel.open').forEach(p => p.classList.remove('open'));
    el.classList.add('open');
    // smooth scroll to opened panel
    setTimeout(() => el.scrollIntoView({behavior: 'smooth', block: 'start'}), 80);
  }
}
</script>

</body>
</html>