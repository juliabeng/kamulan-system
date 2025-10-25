<?php
require_once(__DIR__ . '/../config/db.php');
require_once(__DIR__ . '/../config/session.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if (!$name || !$email || !$password) {
        $err = 'All fields are required.';
    } else {
        // determine role by email pattern
        $role = 'buyer';
        $branch_id = null;
        $mail_user = strtolower(explode('@', $email)[0]); // e.g., juan_rizal

        if (preg_match('/_([a-z0-9]+)$/', $mail_user, $m)) {
            $maybe_branch = $m[1];
            $domain = strtolower(explode('@', $email)[1] ?? '');
            if ($domain === 'rider.com') $role = 'rider';
            if ($domain === 'staff.com') $role = 'staff';
            if ($role === 'rider' || $role === 'staff') {
                // find branch id by matching maybe_branch in branch name
                $stmt = $pdo->prepare('SELECT id FROM branches WHERE LOWER(name) LIKE ? LIMIT 1');
                $stmt->execute(['%' . $maybe_branch . '%']);
                $branch_id = $stmt->fetchColumn() ?: null;
            }
        }

        // ✅ hash the password before saving
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // create user - now storing hashed password
        $stmt = $pdo->prepare('INSERT INTO users (name,email,phone,password,role,branch_id) VALUES (?,?,?,?,?,?)');
        $stmt->execute([$name, $email, $_POST['phone'] ?? '', $hashed_password, $role, $branch_id]);
        header('Location: login.php');
        exit;
    }
}
?>

<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Register</title>
        <link rel="stylesheet" href="/kamulan-system/assets/css/style.css">
        <link rel="stylesheet" href="/kamulan-system/assets/css/login.css">
    </head>
<body>
<main class="form-card">
  <h2>Register</h2>
  <?php if(!empty($err)) echo '<p class="error">'.htmlspecialchars($err).'</p>'; ?>
  <form method="post">
    <label>Name<input name="name" required></label>
    <label>Email<input type="email" name="email" required></label>
    <label>Phone<input name="phone"></label>
    <label>Password<input type="password" name="password" required></label>
    <button class="button" type="submit">Sign up</button>
  </form>
  <p><a href="/kamulan-system/auth/login.php">Login</a></p>
</main>
</body>
</html>
