<?php
include '../includes/conexion.php';
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}

$usuarios = $conn->query("SELECT id, nombre, email, rol FROM usuarios ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Usuarios Registrados</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <nav class="navbar navbar-dark bg-dark px-4">
    <span class="navbar-brand mb-0 h1">Panel de Administración</span>
    <div>
      <a href="index.php" class="btn btn-outline-light btn-sm me-2">Panel Admin</a>
      <a href="../index.php" class="btn btn-outline-light btn-sm">Ir al sitio</a>
    </div>
  </nav>

  <div class="container py-4">
    <h2 class="mb-4">👤 Usuarios Registrados</h2>

    <div class="table-responsive">
      <table class="table table-striped table-bordered align-middle">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Rol</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($usuarios as $user): ?>
            <tr>
              <td><?= $user['id'] ?></td>
              <td><?= htmlspecialchars($user['nombre']) ?></td>
              <td><?= htmlspecialchars($user['email']) ?></td>
              <td><?= ucfirst($user['rol']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
