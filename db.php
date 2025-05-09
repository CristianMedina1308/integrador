<?php
$host = 'localhost';
$dbname = 'tienda_maquillaje';
$username = 'root';
$password = '';

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  echo 'Error de conexión: ' . $e->getMessage();
}
?>
