<?php
include 'header.php';
?>

<div class="container py-5 d-flex justify-content-center align-items-center" style="min-height: 70vh;">
  <div class="text-center">
    <div class="mb-4">
      <i class="bi bi-check-circle-fill text-success" style="font-size: 6rem; animation: bounce 1s infinite;"></i>
    </div>
    <h1 class="mb-3 fw-bold">¡Contraseña actualizada!</h1>
    <p class="text-muted mb-4 fs-5">Tu contraseña se cambió exitosamente.<br>Ahora puedes iniciar sesión con tu nueva clave.</p>
    <a href="login.php" class="btn btn-success btn-lg px-5">Ir a Iniciar Sesión</a>
  </div>
</div>

<!-- Animación pequeña al icono -->
<style>
@keyframes bounce {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-8px);
  }
}
</style>

<?php
include 'footer.php';
?>
