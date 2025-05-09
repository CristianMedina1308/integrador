<?php
include 'header.php';
include 'includes/conexion.php';

// Buscar y filtrar
$busqueda = $_GET['buscar'] ?? '';
$categoria = $_GET['categoria'] ?? '';

$sql = "SELECT * FROM productos WHERE 1";
$params = [];

if ($busqueda) {
  $sql .= " AND nombre LIKE ?";
  $params[] = "%$busqueda%";
}

if ($categoria) {
  $sql .= " AND categoria = ?";
  $params[] = $categoria;
}

$sql .= " ORDER BY id DESC";
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener categorías para filtro
$categorias = $conn->query("SELECT DISTINCT categoria FROM productos")->fetchAll(PDO::FETCH_COLUMN);
?>

<main class="container py-5">
  <h1 class="text-center mb-4">Nuestros Productos</h1>

  <!-- Filtros -->
  <form method="get" class="row g-3 justify-content-center mb-5">
    <div class="col-md-4">
      <input type="text" name="buscar" class="form-control" placeholder="Buscar producto..." value="<?= htmlspecialchars($busqueda) ?>">
    </div>
    <div class="col-md-3">
      <select name="categoria" class="form-select">
        <option value="">Todas las categorías</option>
        <?php foreach ($categorias as $cat): ?>
          <option value="<?= $cat ?>" <?= $cat == $categoria ? 'selected' : '' ?>><?= ucfirst($cat) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-2">
      <button type="submit" class="btn btn-primary w-100">Filtrar</button>
    </div>
  </form>

  <!-- Catálogo -->
  <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
    <?php if ($productos): ?>
      <?php foreach ($productos as $p): ?>
        <div class="col">
          <div class="card h-100 shadow-sm position-relative">
            <!-- Ícono de wishlist -->
            <i
              class="bi bi-heart favorito-toggle position-absolute top-0 end-0 m-2"
              data-id="<?= $p['id'] ?>"
              style="font-size:1.5rem; cursor:pointer;"
            ></i>

            <a href="producto.php?id=<?= $p['id'] ?>">
              <img src="assets/img/productos/<?= htmlspecialchars($p['imagen']) ?>" class="card-img-top" alt="<?= htmlspecialchars($p['nombre']) ?>">
            </a>
            <div class="card-body text-center d-flex flex-column">
              <h5 class="card-title"><?= htmlspecialchars($p['nombre']) ?></h5>
              <p class="card-text text-danger fw-bold">$<?= number_format($p['precio'], 0, ',', '.') ?></p>
              <button class="btn btn-outline-danger mt-auto"
                      onclick="agregarCarrito('<?= htmlspecialchars($p['nombre']) ?>', <?= $p['precio'] ?>, <?= $p['id'] ?>)">
                Agregar al carrito
              </button>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-center text-muted">No se encontraron productos.</p>
    <?php endif; ?>
  </div>
</main>

<?php include 'footer.php'; ?>

<script>
// Al hacer clic en el corazón togglear wishlist
document.body.addEventListener('click', e => {
  if (e.target.matches('.favorito-toggle')) {
    const btn = e.target;
    const productoId = btn.dataset.id;
    fetch('wishlist_toggle.php', {
      method: 'POST',
      headers: {'Content-Type':'application/x-www-form-urlencoded'},
      body: 'producto_id=' + encodeURIComponent(productoId)
    })
    .then(res => res.json())
    .then(json => {
      if (json.status === 'added') {
        btn.classList.replace('bi-heart', 'bi-heart-fill');
        btn.classList.add('text-danger');
      } else if (json.status === 'removed') {
        btn.classList.replace('bi-heart-fill', 'bi-heart');
        btn.classList.remove('text-danger');
      } else if (json.error) {
        alert(json.error);
      }
    });
  }
});
</script>
