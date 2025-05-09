<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}

include '../includes/conexion.php';

$id = $_GET['id'] ?? null;
if (!$id) {
  echo "Pedido no encontrado.";
  exit;
}

$pedido = $conn->prepare("SELECT p.*, u.nombre FROM pedidos p LEFT JOIN usuarios u ON p.usuario_id = u.id WHERE p.id = ?");
$pedido->execute([$id]);
$info = $pedido->fetch(PDO::FETCH_ASSOC);

$detalle = $conn->prepare("SELECT * FROM detalle_pedido WHERE pedido_id = ?");
$detalle->execute([$id]);
$productos = $detalle->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Detalle del Pedido</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <header style="background:#222; color:#fff; padding:20px;">
    <h1>Detalle del Pedido #<?= $info['id'] ?></h1>
    <a href="pedidos.php" style="color:#fff;">← Volver a pedidos</a>
  </header>

  <main style="padding: 30px;">
    <p><strong>Usuario:</strong> <?= htmlspecialchars($info['nombre'] ?? 'No registrado') ?></p>
    <p><strong>Total:</strong> $<?= number_format($info['total'], 2) ?></p>
    <p><strong>Fecha:</strong> <?= $info['fecha'] ?></p>

    <h2>Productos incluidos</h2>
    <table border="1" cellpadding="10" style="width:100%;">
      <tr>
        <th>Producto</th>
        <th>Cantidad</th>
        <th>Precio Unitario</th>
        <th>Subtotal</th>
      </tr>
      <?php foreach ($productos as $p): ?>
      <tr>
        <td><?= htmlspecialchars($p['nombre_producto']) ?></td>
        <td><?= $p['cantidad'] ?></td>
        <td>$<?= number_format($p['precio_unitario'], 2) ?></td>
        <td>$<?= number_format($p['cantidad'] * $p['precio_unitario'], 2) ?></td>
      </tr>
      <?php endforeach; ?>
    </table>
  </main>
</body>
</html>
