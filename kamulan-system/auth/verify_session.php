<?php
require_once(__DIR__ . '/../config/session.php');
if (isset($_SESSION['user'])) echo json_encode(['logged'=>true,'user'=>$_SESSION['user']]); else echo json_encode(['logged'=>false]);
?>
