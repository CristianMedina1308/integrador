<?php
$host = "127.0.0.1:3307"; // o "localhost"
$db = "maquillaje";  // debe existir en phpMyAdmin
$user = "root";
$pass = "";          // sin contraseña en XAMPP por defecto

try {
  $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
  die("Error: " . $e->getMessage());
}
?>
