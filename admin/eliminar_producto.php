<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}

include '../includes/conexion.php';

// Obtener ID
$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
  $mensaje = "ID no válido.";
} else {
  // Buscar producto
  $stmt = $conn->prepare("SELECT imagen, nombre FROM productos WHERE id = ?");
  $stmt->execute([$id]);
  $producto = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($producto) {
    $imagen = $producto['imagen'];
    $rutaImagen = "../assets/img/" . $imagen;

    // Eliminar imagen si existe
    if (file_exists($rutaImagen)) {
      unlink($rutaImagen);
    }

    // Eliminar de la base de datos
    $del = $conn->prepare("DELETE FROM productos WHERE id = ?");
    $del->execute([$id]);

    $mensaje = "✅ Producto eliminado exitosamente.";
    $success = true;
  } else {
    $mensaje = "Producto no encontrado.";
  }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Eliminar producto</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container mt-5">
    <div class="text-center">
      <h2>Eliminar producto</h2>
      <hr>

      <?php if (isset($success) && $success): ?>
        <div class="alert alert-success"><?= $mensaje ?></div>
        <a href="productos.php" class="btn btn-primary mt-3">← Volver a productos</a>
      <?php else: ?>
        <div class="alert alert-danger"><?= $mensaje ?></div>
        <a href="productos.php" class="btn btn-secondary mt-3">← Volver</a>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
