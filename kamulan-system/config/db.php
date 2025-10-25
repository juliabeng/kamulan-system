<?php
// config/db.php
$DB_HOST = 'localhost';   // use localhost instead of 127.0.0.1 for XAMPP
$DB_PORT = '3306';        // default MySQL port
$DB_NAME = 'kamulan_db';
$DB_USER = 'root';
$DB_PASS = '';             // default XAMPP has no password

try {
    $dsn = "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset=utf8mb4";
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("<h3 style='color:red'>❌ DB connection failed:</h3><pre>" . $e->getMessage() . "</pre>");
}
?>
