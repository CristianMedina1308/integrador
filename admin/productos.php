<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}

include '../includes/conexion.php';

// GUARDAR PRODUCTO NUEVO
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nombre = $_POST['nombre'];
  $descripcion = $_POST['descripcion'];
  $precio = $_POST['precio'];
  $categoria = $_POST['categoria'];

  $nombreImg = $_FILES['imagen']['name'];
  $rutaTemporal = $_FILES['imagen']['tmp_name'];
  $rutaFinal = '../assets/img/' . $nombreImg;

  move_uploaded_file($rutaTemporal, $rutaFinal);

  $stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, precio, categoria, imagen) VALUES (?, ?, ?, ?, ?)");
  $stmt->execute([$nombre, $descripcion, $precio, $categoria, $nombreImg]);
}

// LISTAR PRODUCTOS
$productos = $conn->query("SELECT * FROM productos ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Admin - Productos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <nav class="navbar navbar-dark bg-dark px-4">
    <span class="navbar-brand mb-0 h1">Panel de Administración</span>
    <a href="index.php" class="btn btn-outline-light btn-sm">← Volver</a>
  </nav>

  <div class="container py-4">
    <h2 class="mb-4">Agregar nuevo producto</h2>

    <form method="post" enctype="multipart/form-data" class="row g-3 mb-5">
      <div class="col-md-6">
        <label class="form-label">Nombre</label>
        <input type="text" name="nombre" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Precio</label>
        <input type="number" name="precio" step="0.01" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Categoría</label>
        <input type="text" name="categoria" class="form-control">
      </div>
      <div class="col-md-6">
        <label class="form-label">Imagen</label>
        <input type="file" name="imagen" class="form-control" accept="image/*" required>
      </div>
      <div class="col-12">
        <label class="form-label">Descripción</label>
        <textarea name="descripcion" class="form-control" rows="3"></textarea>
      </div>
      <div class="col-12">
        <button type="submit" class="btn btn-primary">Agregar producto</button>
      </div>
    </form>

    <h2 class="mb-3">Productos existentes</h2>

    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle">
        <thead class="table-light">
          <tr>
            <th>Imagen</th>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Categoría</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($productos as $p): ?>
            <tr>
              <td><img src="../assets/img/<?= htmlspecialchars($p['imagen']) ?>" width="60" class="img-thumbnail"></td>
              <td><?= htmlspecialchars($p['nombre']) ?></td>
              <td>$<?= number_format($p['precio'], 2) ?></td>
              <td><?= htmlspecialchars($p['categoria']) ?></td>
              <td>
                <a href="editar_producto.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                <a href="eliminar_producto.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este producto?')">Eliminar</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
