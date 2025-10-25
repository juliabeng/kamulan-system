<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Confirm Logout</title>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f7f7f7;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .box {
      background: white;
      padding: 40px 50px;
      border-radius: 10px;
      text-align: center;
      box-shadow: 0 3px 10px rgba(0,0,0,0.2);
    }
    .btn {
      padding: 10px 20px;
      border-radius: 6px;
      text-decoration: none;
      margin: 10px;
      display: inline-block;
      font-weight: 500;
    }
    .yes {
      background: #4b5320;
      color: white;
    }
    .no {
      background: #ccc;
      color: #333;
    }
    .btn:hover {
      opacity: 0.85;
    }
  </style>
</head>
<body>
  <div class="box">
    <h2>Are you sure you want to logout?</h2>
    <p>You'll be redirected to the homepage after confirming.</p>
    <a href="/kamulan-system/auth/logout.php" class="btn yes">Yes, Logout</a>
    <a href="javascript:history.back()" class="btn no">Cancel</a>
  </div>
</body>
</html>
