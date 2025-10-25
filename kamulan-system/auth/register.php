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
    <title>Register - Kamulan Kitchenette</title>
    <link rel="stylesheet" href="/kamulan-system/assets/css/style.css">
    <link rel="stylesheet" href="/kamulan-system/assets/css/login.css">
    <style>
/* Body with background image, lighter blur and subtle white overlay */
body {
    font-family: "Poppins", sans-serif;
    margin: 0;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
    overflow: hidden;
}

body::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background:
        linear-gradient(rgba(255,255,255,0.1), rgba(255,255,255,0.1)), /* lighter soft white overlay */
        url('/kamulan-system/assets/images/kamulan-backg.jpg') no-repeat center center/cover;
    filter: blur(6px); /* lighter blur */
    z-index: -1;
}

.form-card {
    max-width: 420px;
    width: 90%;
    background: rgba(255,255,255,0.92); /* slightly less opaque white */
    padding: 25px 35px;
    border-radius: 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    text-align: center;
    position: relative;
    z-index: 1;
}

.form-card img {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 6px;
}

.form-card h2 {
    color: #4b5d2a;
    margin-bottom: 6px;
}

.form-card label {
    display: block;
    margin-bottom: 6px;
    text-align: left;
    font-weight: 500;
}

.form-card input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 8px;
    margin-top: 2px;
}

.form-card .button {
    background-color: #556B2F;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    cursor: pointer;
    transition: 0.3s;
    width: 100%;
    font-weight: 600;
}

.form-card .button:hover {
    background-color: #6b7b44;
}

.form-card p {
    margin-top: 10px;
}

.form-card a {
    color: #556B2F;
    text-decoration: none;
    font-weight: 500;
}

.form-card a:hover {
    text-decoration: underline;
}

.error {
    color: red;
    margin-bottom: 6px;
}

@media (max-width: 480px) {
    .form-card {
        padding: 20px;
        width: 95%;
    }

    .form-card img {
        width: 80px;
        height: 80px;
    }
}
</style>

</head>
<body>
<main class="form-card">
  <!-- ✅ Kamulan Logo on top -->
  <img src="/kamulan-system/assets/images/kamulan-logo.jpg" alt="Kamulan Kitchenette Logo">
  <h2>Register</h2>

  <?php if(!empty($err)) echo '<p class="error">'.htmlspecialchars($err).'</p>'; ?>

  <form method="post">
    <label>Name
      <input name="name" required>
    </label>
    <label>Email
      <input type="email" name="email" required>
    </label>
    <label>Phone
      <input name="phone">
    </label>
    <label>Password
      <input type="password" name="password" required>
    </label>
    <button class="button" type="submit">Sign up</button>
  </form>

  <p><a href="/kamulan-system/auth/login.php">Already have an account? Login</a></p>
</main>
</body>
</html>
