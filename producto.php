<?php
include 'header.php';
include 'includes/conexion.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;

if (!$id || $id <= 0) {
  echo "<div class='container py-5 text-center'><p class='text-danger'>Producto no encontrado.</p></div>";
  include 'footer.php';
  exit;
}

// Obtener información del producto
$stmt = $conn->prepare("SELECT * FROM productos WHERE id = ?");
$stmt->execute([$id]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$producto) {
  echo "<div class='container py-5 text-center'><p class='text-danger'>Producto no disponible.</p></div>";
  include 'footer.php';
  exit;
}

// Galería adicional
$imagenes = $conn->prepare("SELECT * FROM producto_imagenes WHERE producto_id = ?");
$imagenes->execute([$id]);
$galeria = $imagenes->fetchAll(PDO::FETCH_ASSOC);

// Guardar reseña
if (isset($_POST['reseñar']) && isset($_SESSION['usuario'])) {
  $usuario_id = $_SESSION['usuario']['id'];
  $puntuacion = $_POST['puntuacion'];
  $comentario = $_POST['comentario'];

  $insert = $conn->prepare("INSERT INTO reseñas (producto_id, usuario_id, puntuacion, comentario) VALUES (?, ?, ?, ?)");
  $insert->execute([$id, $usuario_id, $puntuacion, $comentario]);
  echo "<script>location.reload();</script>";
}
?>

<main class="container py-5">
  <div class="row g-5">
    <!-- Galería -->
    <div class="col-md-6 text-center">
      <div class="border rounded p-3 mb-3">
        <img id="imagen-principal" src="assets/img/productos/<?= $galeria[0]['archivo'] ?? $producto['imagen'] ?>" class="img-fluid rounded" alt="Imagen principal">
      </div>
      <?php if (count($galeria) > 1): ?>
        <div class="d-flex justify-content-center flex-wrap gap-2">
          <?php foreach ($galeria as $img): ?>
            <img src="assets/img/productos/<?= $img['archivo'] ?>" onclick="cambiarImagen(this.src)" class="rounded border" style="width: 60px; height: 60px; cursor: pointer;">
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- Información -->
    <div class="col-md-6">
      <h2><?= htmlspecialchars($producto['nombre']) ?></h2>
      <p><?= nl2br(htmlspecialchars($producto['descripcion'])) ?></p>
      <h4 class="text-danger fw-bold">$<?= number_format($producto['precio'], 0, ',', '.') ?></h4>
      <button class="btn btn-outline-danger mt-3" onclick="agregarCarrito('<?= htmlspecialchars($producto['nombre']) ?>', <?= $producto['precio'] ?>, <?= $producto['id'] ?>)">
        🛒 Agregar al carrito
      </button>
    </div>
  </div>

  <hr class="my-5">

  <!-- Reseñas -->
  <section class="mt-4">
    <h4>⭐ Reseñas de usuarios</h4>
    <?php
    $r = $conn->prepare("SELECT r.*, u.nombre FROM reseñas r JOIN usuarios u ON r.usuario_id = u.id WHERE producto_id = ? ORDER BY fecha DESC");
    $r->execute([$id]);
    $reseñas = $r->fetchAll(PDO::FETCH_ASSOC);

    if ($reseñas):
      foreach ($reseñas as $res): ?>
        <div class="border-start border-4 border-danger-subtle ps-3 mb-3 bg-light rounded py-2">
          <strong><?= htmlspecialchars($res['nombre']) ?></strong> — <?= str_repeat("⭐", (int)$res['puntuacion']) ?>
          <p class="mb-1"><?= htmlspecialchars($res['comentario']) ?></p>
          <small class="text-muted"><?= $res['fecha'] ?></small>
        </div>
    <?php endforeach; else: ?>
      <p class="text-muted">No hay reseñas todavía.</p>
    <?php endif; ?>
  </section>

  <!-- Formulario de reseña -->
  <?php if (isset($_SESSION['usuario'])): ?>
    <section class="mt-5">
      <h5>📝 Dejar una reseña</h5>
      <form method="post" class="mt-3">
        <div class="mb-3">
          <label for="puntuacion" class="form-label">Puntuación</label>
          <select name="puntuacion" class="form-select w-auto" required>
            <option value="">Selecciona</option>
            <?php for ($i = 5; $i >= 1; $i--): ?>
              <option value="<?= $i ?>"><?= $i ?> ⭐</option>
            <?php endfor; ?>
          </select>
        </div>
        <div class="mb-3">
          <label for="comentario" class="form-label">Comentario</label>
          <textarea name="comentario" class="form-control" rows="4" required></textarea>
        </div>
        <button type="submit" name="reseñar" class="btn btn-danger">Enviar reseña</button>
      </form>
    </section>
  <?php else: ?>
    <p class="mt-4">Debes <a href="login.php">iniciar sesión</a> para dejar una reseña.</p>
  <?php endif; ?>
</main>

<script>
function cambiarImagen(src) {
  document.getElementById('imagen-principal').src = src;
}

const zoom = document.getElementById('imagen-principal');
zoom.addEventListener('mousemove', function(e) {
  const rect = zoom.getBoundingClientRect();
  const x = ((e.clientX - rect.left) / rect.width) * 100;
  const y = ((e.clientY - rect.top) / rect.height) * 100;
  zoom.style.transformOrigin = `${x}% ${y}%`;
});
zoom.addEventListener('mouseenter', () => zoom.style.transform = 'scale(2)');
zoom.addEventListener('mouseleave', () => zoom.style.transform = 'scale(1)');
</script>

<?php include 'footer.php'; ?>
