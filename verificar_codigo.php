<?php
include 'includes/conexion.php';
session_start();
include 'header.php';

// Si no hay sesión de recuperación activa, redirigir
if (!isset($_SESSION['codigo_recuperacion'], $_SESSION['telefono_recuperacion'])) {
  header('Location: recuperar.php');
  exit;
}

$mensaje = '';
$exito = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $codigo_ingresado = trim($_POST['codigo'] ?? '');
  $nuevo_password = trim($_POST['nuevo_password'] ?? '');
  $confirmar_password = trim($_POST['confirmar_password'] ?? '');

  if ($codigo_ingresado == $_SESSION['codigo_recuperacion']) {
    if ($nuevo_password === $confirmar_password) {
      if (strlen($nuevo_password) >= 6) {
        $telefono = $_SESSION['telefono_recuperacion'];
        $password_hash = password_hash($nuevo_password, PASSWORD_BCRYPT);

        // Actualizar contraseña en la base de datos
        $stmt = $conn->prepare("UPDATE usuarios SET password = ?, token_recuperacion = NULL WHERE telefono = ?");
        $stmt->execute([$password_hash, $telefono]);

        // Limpiar sesión de recuperación
        unset($_SESSION['codigo_recuperacion'], $_SESSION['telefono_recuperacion']);

        $mensaje = "<div class='alert alert-success text-center'>
          ✅ ¡Contraseña actualizada exitosamente!<br>
          Redireccionando al inicio de sesión en <strong id='countdown'>5</strong> segundos...
        </div>";

        $exito = true;
      } else {
        $mensaje = "<div class='alert alert-warning'>❗ La contraseña debe tener al menos 6 caracteres.</div>";
      }
    } else {
      $mensaje = "<div class='alert alert-danger'>❌ Las contraseñas no coinciden.</div>";
    }
  } else {
    $mensaje = "<div class='alert alert-danger'>❌ El código ingresado es incorrecto.</div>";
  }
}
?>

<div class="container py-5">
  <h1 class="text-center mb-4">🔒 Verificar Código</h1>

  <?php if ($mensaje): ?>
    <div class="mb-4"><?= $mensaje ?></div>
  <?php endif; ?>

  <?php if (!$exito): ?>
    <form method="post" class="mx-auto" style="max-width: 400px;">
      <div class="mb-3">
        <label class="form-label">Código recibido</label>
        <input type="text" name="codigo" class="form-control" required placeholder="Ej: 123456">
      </div>

      <div class="mb-3">
        <label class="form-label">Nueva contraseña</label>
        <input type="password" name="nuevo_password" class="form-control" required placeholder="Mínimo 6 caracteres">
      </div>

      <div class="mb-3">
        <label class="form-label">Confirmar nueva contraseña</label>
        <input type="password" name="confirmar_password" class="form-control" required>
      </div>

      <button type="submit" class="btn btn-primary w-100">Actualizar Contraseña</button>
    </form>
  <?php endif; ?>
</div>

<?php if ($exito): ?>
<script>
// Redirigir automáticamente después de 5 segundos
let contador = 5;
const countdown = document.getElementById('countdown');

const interval = setInterval(() => {
  contador--;
  if (contador <= 0) {
    clearInterval(interval);
    window.location.href = "login.php";
  } else {
    countdown.textContent = contador;
  }
}, 1000);
</script>
<?php endif; ?>

<?php include 'footer.php'; ?>
