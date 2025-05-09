<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: login.php");
  exit;
}

include 'includes/conexion.php';
include 'header.php';

$usuario_id = $_SESSION['usuario']['id'];
$usuario = $_SESSION['usuario'];
$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['actualizar_datos'])) {
    $nuevo_nombre = trim($_POST['nombre']);
    $nuevo_email = trim($_POST['email']);

    if ($nuevo_nombre && $nuevo_email) {
      $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, email = ? WHERE id = ?");
      if ($stmt->execute([$nuevo_nombre, $nuevo_email, $usuario_id])) {
        $_SESSION['usuario']['nombre'] = $nuevo_nombre;
        $_SESSION['usuario']['email'] = $nuevo_email;
        $mensaje = '✅ Datos actualizados correctamente.';
      } else {
        $error = '❌ Error al actualizar datos.';
      }
    } else {
      $error = 'Todos los campos son obligatorios.';
    }
  }

  if (isset($_POST['cambiar_contrasena'])) {
    $actual = $_POST['contrasena_actual'] ?? '';
    $nueva = $_POST['nueva_contrasena'] ?? '';
    $confirmar = $_POST['confirmar_contrasena'] ?? '';

    if ($actual && $nueva && $confirmar) {
      $stmt = $conn->prepare("SELECT contrasena FROM usuarios WHERE id = ?");
      $stmt->execute([$usuario_id]);
      $hash = $stmt->fetchColumn();

      if (password_verify($actual, $hash)) {
        if ($nueva === $confirmar) {
          $nueva_hash = password_hash($nueva, PASSWORD_DEFAULT);
          $upd = $conn->prepare("UPDATE usuarios SET contrasena = ? WHERE id = ?");
          $upd->execute([$nueva_hash, $usuario_id]);
          $mensaje = '✅ Contraseña actualizada correctamente.';
        } else {
          $error = '❌ Las nuevas contraseñas no coinciden.';
        }
      } else {
        $error = '❌ Contraseña actual incorrecta.';
      }
    } else {
      $error = 'Completa todos los campos para cambiar la contraseña.';
    }
  }
}

// Pedidos y Reseñas
$pedidos = $conn->prepare("SELECT * FROM pedidos WHERE usuario_id = ? ORDER BY fecha DESC");
$pedidos->execute([$usuario_id]);

$reseñas = $conn->prepare("
  SELECT r.*, p.nombre AS producto
  FROM reseñas r
  JOIN productos p ON r.producto_id = p.id
  WHERE r.usuario_id = ?
  ORDER BY r.fecha DESC
");
$reseñas->execute([$usuario_id]);
?>

<main class="container py-5">
  <h1 class="text-center mb-5">👤 Mi Perfil</h1>

  <?php if ($mensaje): ?>
    <div class="alert alert-success"><?= $mensaje ?></div>
  <?php elseif ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>

  <!-- Datos personales -->
  <div class="card mb-5 shadow-sm">
    <div class="card-body">
      <h3 class="card-title mb-4">📇 Editar datos personales</h3>
      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Nombre</label>
          <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Correo electrónico</label>
          <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($usuario['email']) ?>" required>
        </div>
        <button type="submit" name="actualizar_datos" class="btn btn-primary">Guardar cambios</button>
      </form>
    </div>
  </div>

  <!-- Cambiar contraseña -->
  <div class="card mb-5 shadow-sm">
    <div class="card-body">
      <h3 class="card-title mb-4">🔒 Cambiar contraseña</h3>
      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Contraseña actual</label>
          <input type="password" name="contrasena_actual" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Nueva contraseña</label>
          <input type="password" name="nueva_contrasena" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Confirmar nueva contraseña</label>
          <input type="password" name="confirmar_contrasena" class="form-control" required>
        </div>
        <button type="submit" name="cambiar_contrasena" class="btn btn-warning">Actualizar contraseña</button>
      </form>
    </div>
  </div>

  <!-- Historial de pedidos -->
  <div class="card mb-5 shadow-sm">
    <div class="card-body">
      <h3 class="card-title mb-4">🧾 Historial de pedidos</h3>
      <?php if ($pedidos->rowCount() > 0): ?>
        <div class="table-responsive">
          <table class="table table-bordered text-center align-middle">
            <thead class="table-light">
              <tr><th>Fecha</th><th>Total</th><th>Estado</th><th>Ver</th></tr>
            </thead>
            <tbody>
              <?php foreach ($pedidos as $p): ?>
                <tr>
                  <td><?= $p['fecha'] ?></td>
                  <td>$<?= number_format($p['total'], 0, ',', '.') ?></td>
                  <td><span class="badge bg-secondary"><?= ucfirst($p['estado']) ?></span></td>
                  <td><a href="ver_pedido.php?id=<?= $p['id'] ?>" class="btn btn-outline-primary btn-sm">Ver</a></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <div class="alert alert-info">No has realizado pedidos aún.</div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Reseñas -->
  <div class="card mb-5 shadow-sm">
    <div class="card-body">
      <h3 class="card-title mb-4">⭐ Mis reseñas</h3>
      <?php if ($reseñas->rowCount() > 0): ?>
        <?php foreach ($reseñas as $res): ?>
          <div class="border-start border-4 border-danger bg-light p-3 rounded mb-3">
            <p class="mb-1"><strong><?= htmlspecialchars($res['producto']) ?></strong> — <?= str_repeat("⭐", (int)$res['puntuacion']) ?></p>
            <p class="mb-1"><?= htmlspecialchars($res['comentario']) ?></p>
            <small class="text-muted"><?= $res['fecha'] ?></small>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="alert alert-secondary">No has dejado reseñas todavía.</div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Favoritos -->
  <div class="card shadow-sm mb-5">
    <div class="card-body">
      <h3 class="card-title mb-4">❤️ Mis favoritos</h3>
      <div id="contenedor-favoritos" class="row g-4"></div>
      <p class="mt-3 text-end"><a href="favoritos.php" class="btn btn-outline-danger">Ver todos en Lista de deseos</a></p>
    </div>
  </div>
</main>

<?php include 'footer.php'; ?>
