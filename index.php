<?php
include 'header.php';
include 'includes/conexion.php';

// Productos destacados (últimos 3 productos agregados)
$destacados = $conn->query("SELECT * FROM productos ORDER BY id DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);

// Categorías
$categorias = $conn->query("SELECT DISTINCT categoria FROM productos")->fetchAll(PDO::FETCH_COLUMN);
?>

<main>

  <!-- 1. Banner / Carousel -->
  <div id="mainCarousel" class="carousel slide mb-5" data-bs-ride="carousel">
    <div class="carousel-inner">
      <div class="carousel-item active">
        <img src="assets/img/banner1.jpg" class="d-block w-100" alt="Maquillaje 1">
        <div class="carousel-caption d-none d-md-block text-start">
          <h1 class="fw-bold">Descubre tu Belleza</h1>
          <p>Explora nuestros productos más vendidos</p>
          <a href="productos.php" class="btn btn-primary">Ver productos</a>
        </div>
      </div>
      <div class="carousel-item">
        <img src="assets/img/banner2.jpg" class="d-block w-100" alt="Maquillaje 2">
        <div class="carousel-caption d-none d-md-block text-start">
          <h1 class="fw-bold">Maquillaje Profesional</h1>
          <p>Calidad, elegancia y estilo para ti</p>
          <a href="productos.php" class="btn btn-light">Ver colección</a>
        </div>
      </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
      <span class="carousel-control-next-icon"></span>
    </button>
  </div>

  <!-- 2. Productos destacados -->
  <section class="container py-5">
    <h2 class="text-center mb-4">🌟 Productos Destacados</h2>
    <div class="row row-cols-1 row-cols-md-3 g-4">
      <?php foreach ($destacados as $p): ?>
        <div class="col">
          <div class="card h-100 shadow-sm">
            <img src="assets/img/productos/<?= $p['imagen'] ?>" class="card-img-top" alt="<?= htmlspecialchars($p['nombre']) ?>">
            <div class="card-body d-flex flex-column">
              <h5 class="card-title"><?= htmlspecialchars($p['nombre']) ?></h5>
              <p class="card-text text-muted">$<?= number_format($p['precio'], 0, ',', '.') ?></p>
              <a href="producto.php?id=<?= $p['id'] ?>" class="btn btn-outline-primary mt-auto">Ver más</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- 3. Categorías -->
  <section class="bg-light py-5">
    <div class="container">
      <h2 class="text-center mb-4">🧴 Categorías Populares</h2>
      <div class="row row-cols-2 row-cols-md-4 g-4 text-center">
        <?php foreach ($categorias as $cat): ?>
          <div class="col">
            <a href="productos.php?categoria=<?= urlencode($cat) ?>" class="text-decoration-none text-dark">
              <div class="border rounded p-4 bg-white shadow-sm h-100 d-flex align-items-center justify-content-center">
                <strong><?= ucfirst($cat) ?></strong>
              </div>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- 4. Beneficios / Garantías -->
  <section class="container py-5 text-center">
    <div class="row g-4">
      <div class="col-md-4">
        <div class="p-4 border rounded shadow-sm h-100">
          <i class="fas fa-shipping-fast fa-2x mb-3 text-primary"></i>
          <h5>Envíos Rápidos</h5>
          <p>Entregas en 48-72h a toda Colombia</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="p-4 border rounded shadow-sm h-100">
          <i class="fas fa-lock fa-2x mb-3 text-primary"></i>
          <h5>Pago Seguro</h5>
          <p>Transacciones protegidas y confiables</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="p-4 border rounded shadow-sm h-100">
          <i class="fas fa-star fa-2x mb-3 text-primary"></i>
          <h5>Clientes Satisfechos</h5>
          <p>Reseñas 5 estrellas de nuestros compradores</p>
        </div>
      </div>
    </div>
  </section>

  <!-- 5. CTA final -->
  <section class="text-center py-5 bg-primary text-white">
    <h2 class="mb-3">¿Lista para brillar?</h2>
    <p class="mb-4">Encuentra el maquillaje ideal para ti y luce radiante todos los días</p>
    <a href="productos.php" class="btn btn-light btn-lg">Explorar ahora</a>
  </section>

</main>

<?php include 'footer.php'; ?>
