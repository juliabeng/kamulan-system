<?php
require_once(__DIR__ . '/../config/db.php');
require_once(__DIR__ . '/../config/session.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if (!$email || !$password) $err = 'Email and password required';
    else {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if (!$user) $err = 'User not found';
        else {
            $valid = false;

            // ✅ verify hashed password
            if (password_verify($password, $user['password'])) $valid = true;
            // fallback for old plain-text passwords (optional)
            elseif ($password === $user['password']) $valid = true;

            if ($valid) {
                unset($user['password']);
                $_SESSION['user'] = $user;
                // redirect per role
                switch ($user['role']) {
                    case 'rider': header('Location: /kamulan-system/rider/dashboard.php'); exit;
                    case 'staff': header('Location: /kamulan-system/staff/dashboard.php'); exit;
                    case 'manager': header('Location: /kamulan-system/admin/dashboard.php'); exit;
                    default: header('Location: /kamulan-system/buyer/index.php'); exit;
                }
            } else $err = 'Invalid password';
        }
    }
}
?>

<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Login</title>
        <link rel="stylesheet" href="/kamulan-system/assets/css/style.css">
        <link rel="stylesheet" href="/kamulan-system/assets/css/login.css">
    </head>
<body>
<main class="form-card">
  <h2>Login</h2>
  <?php if(!empty($err)) echo '<p class="error">'.htmlspecialchars($err).'</p>'; ?>
  <form method="post">
    <label>Email<input type="email" name="email" required></label>
    <label>Password<input type="password" name="password" required></label>
    <button class="button" type="submit">Log in</button>
  </form>
  <p><a href="/kamulan-system/auth/register.php">Register</a></p>
</main>
</body>
</html>
