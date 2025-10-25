<?php
require_once(__DIR__ . '/../config/session.php');
require_once(__DIR__ . '/../config/db.php');
require_role(['manager']);

// --- FILTER USERS ---
$filter = isset($_GET['role']) && $_GET['role'] !== 'all' ? $_GET['role'] : null;
if ($filter) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE role = ? ORDER BY id DESC");
    $stmt->execute([$filter]);
    $users = $stmt->fetchAll();
} else {
    $users = $pdo->query("SELECT * FROM users ORDER BY id DESC")->fetchAll();
}

// --- DELETE USER ---
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header('Location: manage_users.php');
    exit();
}

// --- UPDATE USER ---
if (isset($_POST['update'])) {
    $stmt = $pdo->prepare("UPDATE users SET name=?, email=?, role=? WHERE id=?");
    $stmt->execute([
        $_POST['name'],
        $_POST['email'],
        $_POST['role'],
        $_POST['id']
    ]);
    header('Location: manage_users.php');
    exit();
}

// --- ADD USER ---
if (isset($_POST['add'])) {
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, created_at)
                           VALUES (?, ?, ?, ?, NOW())");
    $hashed = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $stmt->execute([
        $_POST['name'],
        $_POST['email'],
        $hashed,
        $_POST['role']
    ]);
    header('Location: manage_users.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Users - Kamulan Kitchenette</title>
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
  h2 {
    color: #4a3b20;
    text-align: center;
    margin-bottom: 15px;
  }
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
  .btn {
    padding: 6px 12px;
    border-radius: 6px;
    color: white;
    text-decoration: none;
    margin: 0 3px;
    display: inline-block;
  }
  .btn-edit { background: #007bff; }
  .btn-delete { background: #dc3545; }
  .btn-add {
    background: #6b4f2c;
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    color: #fff;
    cursor: pointer;
  }
  .top-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    margin-bottom: 10px;
  }
  .filter {
    display: flex;
    align-items: center;
    gap: 8px;
  }
  select {
    padding: 6px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 14px;
  }
  .modal {
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.4);
    justify-content: center;
    align-items: center;
  }
  .modal-content {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    width: 400px;
  }
  .modal-content input, .modal-content select {
    width: 100%;
    margin: 6px 0;
    padding: 8px;
    border-radius: 6px;
    border: 1px solid #ccc;
  }
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
</style>
</head>
<body>
<?php include __DIR__ . '/../_partials/header_staff.php'; ?>

<div class="container">
  <!-- Back button -->
  <a href="../admin/dashboard.php" class="back-btn">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
    </svg>
    Back
  </a>

  <div class="top-bar">
    <h2>Manage Users</h2>

    <div class="filter">
      <form method="GET" id="filterForm">
        <select name="role" onchange="document.getElementById('filterForm').submit()">
          <option value="all" <?= !$filter ? 'selected' : '' ?>>All</option>
          <option value="buyer" <?= $filter === 'buyer' ? 'selected' : '' ?>>Buyers</option>
          <option value="rider" <?= $filter === 'rider' ? 'selected' : '' ?>>Riders</option>
          <option value="staff" <?= $filter === 'staff' ? 'selected' : '' ?>>Staff</option>
          <option value="manager" <?= $filter === 'manager' ? 'selected' : '' ?>>Managers</option>
        </select>
      </form>
      <button class="btn-add" onclick="openAdd()">+ Add User</button>
    </div>
  </div>

  <table>
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Email</th>
      <th>Role</th>
      <th>Actions</th>
    </tr>
    <?php foreach ($users as $u): ?>
    <tr>
      <td><?= $u['id'] ?></td>
      <td><?= htmlspecialchars($u['name']) ?></td>
      <td><?= htmlspecialchars($u['email']) ?></td>
      <td><?= ucfirst($u['role']) ?></td>
      <td>
        <a href="#" class="btn btn-edit" onclick='openEdit(<?= json_encode($u) ?>)'>Edit</a>
        <a href="?delete=<?= $u['id'] ?>" class="btn btn-delete" onclick="return confirm('Delete this user?')">Delete</a>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>

<!-- ADD MODAL -->
<div class="modal" id="addModal">
  <div class="modal-content">
    <h3>Add User</h3>
    <form method="POST">
      <input type="text" name="name" placeholder="Full name" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <select name="role" required>
        <option value="buyer">Buyer</option>
        <option value="rider">Rider</option>
        <option value="staff">Staff</option>
        <option value="manager">Manager</option>
      </select>
      <br>
      <button type="submit" name="add" class="btn-add">Add</button>
      <button type="button" class="btn-delete" onclick="closeModal('addModal')">Cancel</button>
    </form>
  </div>
</div>

<!-- EDIT MODAL -->
<div class="modal" id="editModal">
  <div class="modal-content">
    <h3>Edit User</h3>
    <form method="POST">
      <input type="hidden" name="id" id="editId">
      <input type="text" name="name" id="editName" required>
      <input type="email" name="email" id="editEmail" required>
      <select name="role" id="editRole" required>
        <option value="buyer">Buyer</option>
        <option value="rider">Rider</option>
        <option value="staff">Staff</option>
        <option value="manager">Manager</option>
      </select>
      <br>
      <button type="submit" name="update" class="btn-edit">Save</button>
      <button type="button" class="btn-delete" onclick="closeModal('editModal')">Cancel</button>
    </form>
  </div>
</div>

<script>
function openAdd() {
  document.getElementById('addModal').style.display = 'flex';
}
function openEdit(user) {
  document.getElementById('editModal').style.display = 'flex';
  document.getElementById('editId').value = user.id;
  document.getElementById('editName').value = user.name;
  document.getElementById('editEmail').value = user.email;
  document.getElementById('editRole').value = user.role;
}
function closeModal(id) {
  document.getElementById(id).style.display = 'none';
}
</script>

</body>
</html>
