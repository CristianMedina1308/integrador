<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario'])) {
  echo json_encode(['error' => 'Debes iniciar sesión.']);
  exit;
}

require 'includes/conexion.php';

$usuario_id  = $_SESSION['usuario']['id'];
$producto_id = isset($_POST['producto_id']) ? (int)$_POST['producto_id'] : 0;

if (!$producto_id) {
  echo json_encode(['error' => 'Producto inválido.']);
  exit;
}

// ¿Ya está en wishlist?
$stmt = $conn->prepare("SELECT 1 FROM wishlist WHERE usuario_id = ? AND producto_id = ?");
$stmt->execute([$usuario_id, $producto_id]);

if ($stmt->fetch()) {
  // quitar
  $del = $conn->prepare("DELETE FROM wishlist WHERE usuario_id = ? AND producto_id = ?");
  $del->execute([$usuario_id, $producto_id]);
  echo json_encode(['status' => 'removed']);
} else {
  // añadir
  $ins = $conn->prepare("INSERT INTO wishlist (usuario_id, producto_id) VALUES (?, ?)");
  $ins->execute([$usuario_id, $producto_id]);
  echo json_encode(['status' => 'added']);
}
