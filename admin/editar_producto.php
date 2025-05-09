<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}

include '../includes/conexion.php';

// Obtener ID del producto
$id = $_GET['id'] ?? null;

if (!$id) {
  echo "ID no válido.";
  exit;
}

// Obtener datos actuales
$stmt = $conn->prepare("SELECT * FROM productos WHERE id = ?");
$stmt->execute([$id]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$producto) {
  echo "Producto no encontrado.";
  exit;
}

// Actualizar producto
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nombre = $_POST['nombre'];
  $descripcion = $_POST['descripcion'];
  $precio = $_POST['precio'];
  $categoria = $_POST['categoria'];

  if (!empty($_FILES['imagen']['name'])) {
    $imagen = $_FILES['imagen']['name'];
    $tmp = $_FILES['imagen']['tmp_name'];
    move_uploaded_file($tmp, "../assets/img/" . $imagen);
  } else {
    $imagen = $producto['imagen'];
  }

  $update = $conn->prepare("UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, categoria = ?, imagen = ? WHERE id = ?");
  $update->execute([$nombre, $descripcion, $precio, $categoria, $imagen, $id]);

  header("Location: productos.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar producto</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <nav class="navbar navbar-dark bg-dark px-4">
    <span class="navbar-brand mb-0 h1">🛍️ Editar Producto</span>
    <a href="productos.php" class="btn btn-outline-light btn-sm">← Volver</a>
  </nav>

  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card shadow">
          <div class="card-body">
            <form method="post" enctype="multipart/form-data">
              <div class="mb-3">
                <label class="form-label">Nombre del producto</label>
                <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($producto['nombre']) ?>" required>
              </div>

              <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="3"><?= htmlspecialchars($producto['descripcion']) ?></textarea>
              </div>

              <div class="mb-3">
                <label class="form-label">Precio</label>
                <input type="number" name="precio" class="form-control" step="0.01" value="<?= $producto['precio'] ?>" required>
              </div>

              <div class="mb-3">
                <label class="form-label">Categoría</label>
                <input type="text" name="categoria" class="form-control" value="<?= htmlspecialchars($producto['categoria']) ?>">
              </div>

              <div class="mb-3">
                <label class="form-label">Imagen actual</label><br>
                <img src="../assets/img/<?= htmlspecialchars($producto['imagen']) ?>" width="100" class="img-thumbnail">
              </div>

              <div class="mb-3">
                <label class="form-label">Reemplazar imagen (opcional)</label>
                <input type="file" name="imagen" class="form-control" accept="image/*">
              </div>

              <div class="d-grid">
                <button type="submit" class="btn btn-primary">💾 Guardar cambios</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
