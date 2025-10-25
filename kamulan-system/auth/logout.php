<?php
require_once(__DIR__ . '/../config/session.php');
session_destroy();
header('Location: /kamulan-system/index.php');
exit;
?>
