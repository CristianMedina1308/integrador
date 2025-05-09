<?php
include 'includes/conexion.php';
include 'header.php';

$token = $_GET['token'] ?? '';
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $token = $_POST['token'];
  $nueva = $_POST['nueva'];
  $confirmar = $_POST['confirmar'];

  if ($nueva === $confirmar && strlen($nueva) >= 6) {
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE token_recuperacion = ?");
    $stmt->execute([$token]);

    if ($stmt->rowCount() > 0) {
      $hash = password_hash($nueva, PASSWORD_DEFAULT);
      $conn->prepare("UPDATE usuarios SET clave = ?, token_recuperacion = NULL WHERE token_recuperacion = ?")
        ->execute([$hash, $token]);

      $mensaje = "✅ Contraseña actualizada. <a href='login.php'>Inicia sesión</a>";
    } else {
      $mensaje = "❌ Token inválido o expirado.";
    }
  } else {
    $mensaje = "❗ Las contraseñas no coinciden o son demasiado cortas.";
  }
}
?>

<div class="container py-5">
  <h1 class="text-center mb-4">🔒 Restablecer Contraseña</h1>
  <?php if ($mensaje): ?>
    <div class="alert alert-info"><?= $mensaje ?></div>
  <?php endif; ?>
  <?php if (!$mensaje && $token): ?>
    <form method="post" class="mx-auto" style="max-width: 400px;">
      <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
      <div class="mb-3">
        <label class="form-label">Nueva contraseña</label>
        <input type="password" name="nueva" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Confirmar contraseña</label>
        <input type="password" name="confirmar" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-success w-100">Cambiar contraseña</button>
    </form>
  <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
