<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tienda de Maquillaje</title>

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Estilos personalizados -->
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/responsive.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center" href="index.php">
      <img src="assets/img/logo.png" alt="Logo" height="40" class="me-2">
      <span class="d-none d-sm-inline">Maquillaje</span>
    </a>

    <!-- Botón carrito (siempre visible) -->
    <div class="d-flex align-items-center order-lg-2 ms-auto">
      <button class="btn btn-outline-light position-relative me-2" id="btnMiniCarrito" aria-label="Carrito">
        <i class="bi bi-bag-fill"></i>
        <span id="contador-carrito" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">0</span>
      </button>
    </div>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse order-lg-1" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
        <li class="nav-item"><a class="nav-link" href="productos.php">Productos</a></li>
        <li class="nav-item"><a class="nav-link" href="contacto.php">Contacto</a></li>
        <li class="nav-item"><a class="nav-link" href="carrito.php">Carrito</a></li>

        <?php if (isset($_SESSION['usuario'])): ?>
          <li class="nav-item"><a class="nav-link" href="perfil.php">👤 Perfil</a></li>
          <?php if ($_SESSION['usuario']['rol'] === 'admin'): ?>
            <li class="nav-item"><a class="nav-link" href="admin/index.php">Admin</a></li>
          <?php endif; ?>
          <li class="nav-item"><a class="nav-link" href="logout.php">Salir</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="login.php">Ingresar</a></li>
          <li class="nav-item"><a class="nav-link" href="registro.php">Registrarse</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<!-- Mini carrito flotante -->
<div id="mini-carrito" class="mini-carrito shadow rounded p-3 bg-white"
     style="position:fixed; top:70px; right:20px; z-index:1050; display:none; width:300px; max-height:400px; overflow-y:auto;">
  <h5 class="mb-3">🛍️ Tu Carrito</h5>
  <ul id="lista-mini-carrito" class="list-unstyled mb-3"></ul>
  <a href="checkout.php" class="btn btn-danger w-100">Finalizar compra</a>
</div>

<!-- Botón flotante de WhatsApp -->
<a href="https://wa.me/573023341713" class="whatsapp-float" target="_blank" title="¿Necesitas ayuda?">
  <i class="bi bi-whatsapp"></i>
</a>

<!-- Estilo para WhatsApp -->
<style>
  .whatsapp-float {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background-color: #25D366;
    color: white;
    border-radius: 50%;
    font-size: 28px;
    padding: 14px 16px;
    z-index: 9999;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    transition: transform 0.3s ease;
  }

  .whatsapp-float:hover {
    transform: scale(1.1);
    color: white;
    text-decoration: none;
  }

  @media (max-width: 480px) {
    .whatsapp-float {
      font-size: 22px;
      padding: 12px 14px;
      bottom: 15px;
      right: 15px;
    }
  }
</style>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script de mini carrito -->
<script>
  function toggleMiniCarrito() {
    const mini = document.getElementById("mini-carrito");
    mini.style.display = mini.style.display === "none" || mini.style.display === "" ? "block" : "none";
  }

  document.addEventListener("DOMContentLoaded", () => {
    const btnCarrito = document.getElementById("btnMiniCarrito");
    if (btnCarrito) {
      btnCarrito.addEventListener("click", toggleMiniCarrito);
    }
  });
</script>
