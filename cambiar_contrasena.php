<?php
include 'includes/conexion.php';
include 'header.php';

// Validar que haya sesión activa de recuperación
if (!isset($_SESSION['telefono_recuperacion']) || !isset($_SESSION['codigo_recuperacion'])) {
  header("Location: recuperar.php");
  exit;
}

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $codigo = trim($_POST['codigo'] ?? '');
  $nueva_contrasena = trim($_POST['nueva_contrasena'] ?? '');
  $confirmar_contrasena = trim($_POST['confirmar_contrasena'] ?? '');

  if ($codigo == $_SESSION['codigo_recuperacion']) {
    if ($nueva_contrasena && $confirmar_contrasena) {
      if ($nueva_contrasena === $confirmar_contrasena) {
        $telefono = $_SESSION['telefono_recuperacion'];
        $hashed = password_hash($nueva_contrasena, PASSWORD_BCRYPT);

        $stmt = $conn->prepare("UPDATE usuarios SET password = ?, token_recuperacion = NULL WHERE telefono = ?");
        $stmt->execute([$hashed, $telefono]);

        // Eliminar la sesión usada para recuperación
        unset($_SESSION['telefono_recuperacion']);
        unset($_SESSION['codigo_recuperacion']);

        $mensaje = "<div class='alert alert-success'>✅ Tu contraseña ha sido cambiada exitosamente. <a href='login.php'>Inicia sesión aquí</a>.</div>";
      } else {
        $mensaje = "<div class='alert alert-danger'>❌ Las contraseñas no coinciden.</div>";
      }
    } else {
      $mensaje = "<div class='alert alert-warning'>❗ Completa todos los campos.</div>";
    }
  } else {
    $mensaje = "<div class='alert alert-danger'>❌ Código incorrecto.</div>";
  }
}
?>

<div class="container py-5">
  <h1 class="text-center mb-4">🔒 Cambiar Contraseña</h1>

  <?php if ($mensaje): ?>
    <div class="text-center mb-4">
      <?= $mensaje ?>
    </div>
  <?php endif; ?>

  <form method="post" class="mx-auto" style="max-width: 400px;">
    <div class="mb-3">
      <label class="form-label">Código de verificación</label>
      <input type="text" name="codigo" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Nueva contraseña</label>
      <input type="password" name="nueva_contrasena" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Confirmar nueva contraseña</label>
      <input type="password" name="confirmar_contrasena" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary w-100">Actualizar Contraseña</button>
  </form>
</div>

<?php include 'footer.php'; ?>
