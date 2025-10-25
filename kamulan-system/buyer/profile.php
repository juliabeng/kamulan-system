<?php
require_once(__DIR__ . '/../config/session.php');
require_once(__DIR__ . '/../config/db.php');
require_login();
$user = $_SESSION['user'];
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $stmt = $pdo->prepare('UPDATE users SET name=?,phone=? WHERE id=?');
    $stmt->execute([$_POST['name'],$_POST['phone'],$user['id']]);
    $_SESSION['user'] = $pdo->prepare('SELECT * FROM users WHERE id=?')->execute([$user['id']]) ? $_SESSION['user'] : $_SESSION['user'];
    $msg = 'Saved';
}
?>
<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Profile</title>
    <link rel="stylesheet" href="/kamulan-system/assets/css/style.css">
  </head>
  <body>
    <?php include __DIR__ . '/../_partials/header.php'; ?>
<main class="container">
  <h2>Profile</h2>
  <?php if(!empty($msg)) echo '<p class="success">'.htmlspecialchars($msg).'</p>'; ?>
  <form method="post">
    <label>Name<input name="name" value="<?=htmlspecialchars($user['name'])?>"></label>
    <label>Phone<input name="phone" value="<?=htmlspecialchars($user['phone'])?>"></label>
    <button class="button" type="submit">Save</button>
  </form>
</main>
</body>
</html>
