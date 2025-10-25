<?php
require_once(__DIR__ . '/../config/session.php');
require_once(__DIR__ . '/../config/db.php');
require_role(['manager']);

// --- DELETE ITEM ---
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM menu_items WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header('Location: manage_menu.php');
    exit();
}

// --- TOGGLE AVAILABLE (AJAX) ---
if (isset($_POST['toggle_id'])) {
    $id = (int)$_POST['toggle_id'];
    $stmt = $pdo->prepare("UPDATE menu_items SET available = NOT available WHERE id = ?");
    $stmt->execute([$id]);
    echo "success";
    exit();
}

// --- UPDATE ITEM ---
if (isset($_POST['update'])) {
    $stmt = $pdo->prepare("UPDATE menu_items 
        SET name = ?, description = ?, price = ?, category = ?, available = ?
        WHERE id = ?");
    $stmt->execute([
        $_POST['name'],
        $_POST['description'],
        $_POST['price'],
        $_POST['category'],
        $_POST['available'],
        $_POST['id']
    ]);
    header('Location: manage_menu.php');
    exit();
}

// --- ADD ITEM ---
if (isset($_POST['add'])) {
    $stmt = $pdo->prepare("INSERT INTO menu_items (name, description, price, category, available, image, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([
        $_POST['name'],
        $_POST['description'],
        $_POST['price'],
        $_POST['category'],
        $_POST['available'],
        $_POST['image']
    ]);
    header('Location: manage_menu.php');
    exit();
}

// --- FILTERS ---
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$categoryFilter = isset($_GET['category']) ? trim($_GET['category']) : '';

$query = "SELECT * FROM menu_items WHERE 1";
$params = [];

if ($search !== '') {
    $query .= " AND (name LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($categoryFilter !== '') {
    $query .= " AND category = ?";
    $params[] = $categoryFilter;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$items = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Menu - Kamulan Kitchenette</title>
<style>
  body {
    font-family: 'Poppins', sans-serif;
    background: #f7f7f7;
    margin: 0;
    padding: 0;
  }
  .container {
    width: 90%;
    margin: 30px auto;
    background: #fff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }
  h2 { text-align: center; color: #333; }
  .filters {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    margin-bottom: 20px;
    align-items: center;
  }
  .category-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
  }
  .category-buttons a {
    background: #eee;
    color: #333;
    padding: 6px 12px;
    border-radius: 6px;
    text-decoration: none;
  }
  .category-buttons a.active, .category-buttons a:hover {
    background: #6b4f2c;
    color: #fff;
  }
  .search-group {
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .search-form input {
    padding: 8px;
    border-radius: 6px;
    border: 1px solid #ccc;
  }
  .search-form button, .btn-add {
    padding: 8px 16px;
    background: #6b4f2c;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
  }
  table {
    width: 100%;
    border-collapse: collapse;
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
  .btn {
    padding: 6px 12px;
    border-radius: 6px;
    color: #fff;
    text-decoration: none;
  }
  .btn-edit { background: #007bff; }
  .btn-delete { background: #dc3545; }
  .toggle {
    cursor: pointer;
    padding: 5px 10px;
    border-radius: 6px;
    color: white;
    font-weight: bold;
  }
  .toggle.yes { background: #4caf50; }
  .toggle.no { background: #d9534f; }
  .modal {
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.5);
    justify-content: center;
    align-items: center;
  }
  .modal-content {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    width: 400px;
  }
  .modal-content input, .modal-content textarea, .modal-content select {
    width: 100%;
    margin: 6px 0;
    padding: 8px;
    border-radius: 6px;
    border: 1px solid #ccc;
  }
  .back-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: none;
  border: none;
  color: #6b4f2c;
  font-weight: 600;
  cursor: pointer;
  margin-bottom: 10px;
  font-size: 15px;
  text-decoration: none;
  transition: color 0.2s ease;
  }
  .back-btn:hover {
    color: #4a3b20;
  }
  .back-btn svg {
    width: 18px;
    height: 18px;
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

  <h2>Manage Menu</h2>

  <div class="filters">
    <div class="category-buttons">
      <?php
      $categories = ['All', 'Solo Menu', 'Sharing', 'Main Dish', 'Sandwich', 'Dessert', 'Platters', 'Add-ons', 'Drinks'];
      foreach ($categories as $cat):
        $link = $cat == 'All' ? 'manage_menu.php' : '?category=' . urlencode($cat);
        $active = ($categoryFilter == $cat || ($cat == 'All' && $categoryFilter == '')) ? 'active' : '';
      ?>
        <a href="<?= $link ?>" class="<?= $active ?>"><?= $cat ?></a>
      <?php endforeach; ?>
    </div>

    <div class="search-group">
      <form class="search-form" method="GET" action="manage_menu.php">
        <input type="text" name="search" placeholder="Search food..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
      </form>
      <button class="btn-add" onclick="openAdd()">+ Add Product</button>
    </div>
  </div>

  <table>
    <tr>
      <th>Name</th>
      <th>Description</th>
      <th>Price</th>
      <th>Category</th>
      <th>Available</th>
      <th>Actions</th>
    </tr>
    <?php foreach ($items as $item): ?>
    <tr>
      <td><?= htmlspecialchars($item['name']) ?></td>
      <td><?= htmlspecialchars($item['description']) ?></td>
      <td>₱<?= number_format($item['price'], 2) ?></td>
      <td><?= htmlspecialchars($item['category']) ?></td>
      <td>
        <span class="toggle <?= $item['available'] ? 'yes' : 'no' ?>" 
              onclick="toggleAvailable(<?= $item['id'] ?>, this)">
          <?= $item['available'] ? 'Yes' : 'No' ?>
        </span>
      </td>
      <td>
        <a href="#" class="btn btn-edit" onclick='openEdit(<?= json_encode($item) ?>)'>Edit</a>
        <a href="?delete=<?= $item['id'] ?>" class="btn btn-delete" onclick="return confirm('Delete this item?')">Delete</a>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>

<!-- EDIT MODAL -->
<div class="modal" id="editModal">
  <div class="modal-content">
    <h3>Edit Menu Item</h3>
    <form method="POST">
      <input type="hidden" name="id" id="editId">
      <label>Name:</label>
      <input type="text" name="name" id="editName" required>
      <label>Description:</label>
      <textarea name="description" id="editDescription" required></textarea>
      <label>Price:</label>
      <input type="number" step="0.01" name="price" id="editPrice" required>
      <label>Category:</label>
      <select name="category" id="editCategory">
        <?php foreach ($categories as $cat): if ($cat != 'All'): ?>
          <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
        <?php endif; endforeach; ?>
      </select>
      <label>Available:</label>
      <select name="available" id="editAvailable">
        <option value="1">Yes</option>
        <option value="0">No</option>
      </select>
      <br>
      <button type="submit" name="update" class="btn btn-edit">Save Changes</button>
      <button type="button" class="btn btn-delete" onclick="closeModal('editModal')">Cancel</button>
    </form>
  </div>
</div>

<!-- ADD MODAL -->
<div class="modal" id="addModal">
  <div class="modal-content">
    <h3>Add New Product</h3>
    <form method="POST">
      <label>Name:</label>
      <input type="text" name="name" required>
      <label>Description:</label>
      <textarea name="description" required></textarea>
      <label>Price:</label>
      <input type="number" step="0.01" name="price" required>
      <label>Category:</label>
      <select name="category">
        <?php foreach ($categories as $cat): if ($cat != 'All'): ?>
          <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
        <?php endif; endforeach; ?>
      </select>
      <label>Available:</label>
      <select name="available">
        <option value="1">Yes</option>
        <option value="0">No</option>
      </select>
      <label>Image filename:</label>
      <input type="text" name="image" placeholder="e.g., adobo.jpg">
      <br>
      <button type="submit" name="add" class="btn btn-edit">Add Product</button>
      <button type="button" class="btn btn-delete" onclick="closeModal('addModal')">Cancel</button>
    </form>
  </div>
</div>

<script>
function openEdit(item) {
  document.getElementById('editModal').style.display = 'flex';
  document.getElementById('editId').value = item.id;
  document.getElementById('editName').value = item.name;
  document.getElementById('editDescription').value = item.description;
  document.getElementById('editPrice').value = item.price;
  document.getElementById('editCategory').value = item.category;
  document.getElementById('editAvailable').value = item.available;
}

function openAdd() {
  document.getElementById('addModal').style.display = 'flex';
}

function closeModal(id) {
  document.getElementById(id).style.display = 'none';
}

// ✅ Toggle Available instantly via AJAX
function toggleAvailable(id, el) {
  fetch('manage_menu.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'toggle_id=' + id
  })
  .then(res => res.text())
  .then(res => {
    if (res.trim() === 'success') {
      if (el.classList.contains('yes')) {
        el.classList.remove('yes');
        el.classList.add('no');
        el.textContent = 'No';
      } else {
        el.classList.remove('no');
        el.classList.add('yes');
        el.textContent = 'Yes';
      }
    }
  });
}
</script>
</body>
</html>
