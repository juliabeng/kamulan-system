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
            elseif ($password === $user['password']) $valid = true; // fallback

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
    <title>Login - Kamulan Kitchenette</title>
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
          max-width: 400px;
          width: 90%;
          background: rgba(255,255,255,0.92); /* slightly less opaque white */
          padding: 25px 30px;
          border-radius: 12px;
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
          margin-bottom: 10px;
      }

      .form-card h2 {
          color: #4b5d2a;
          margin-bottom: 10px;
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
          width: 100%;
          font-weight: 600;
          transition: 0.3s;
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
  <img src="/kamulan-system/assets/images/kamulan-logo.jpg" alt="Kamulan Kitchenette Logo">
  <h2>Login</h2>

  <?php if(!empty($err)) echo '<p class="error">'.htmlspecialchars($err).'</p>'; ?>

  <form method="post">
    <label>Email
      <input type="email" name="email" required>
    </label>
    <label>Password
      <input type="password" name="password" required>
    </label>
    <button class="button" type="submit">Log in</button>
  </form>
  
  <p><a href="/kamulan-system/auth/register.php">Register</a></p>
</main>
</body>
</html>
