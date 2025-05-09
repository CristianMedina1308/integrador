<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: login.php");
  exit;
}

include 'includes/conexion.php';
include 'header.php';

$id_pedido = $_GET['id'] ?? null;
$usuario_id = $_SESSION['usuario']['id'];
$es_admin = $_SESSION['usuario']['rol'] === 'admin';

if (!$id_pedido) {
  echo "<div class='container py-5'><div class='alert alert-danger text-center'>Pedido no encontrado.</div></div>";
  include 'footer.php';
  exit;
}

if ($es_admin) {
  $stmt = $conn->prepare("SELECT * FROM pedidos WHERE id = ?");
  $stmt->execute([$id_pedido]);
} else {
  $stmt = $conn->prepare("SELECT * FROM pedidos WHERE id = ? AND usuario_id = ?");
  $stmt->execute([$id_pedido, $usuario_id]);
}
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pedido) {
  echo "<div class='container py-5'><div class='alert alert-warning text-center'>Pedido no disponible.</div></div>";
  include 'footer.php';
  exit;
}

// Cancelar pedido
if (isset($_POST['cancelar']) && !$es_admin && $pedido['estado'] === 'pendiente') {
  $update = $conn->prepare("UPDATE pedidos SET estado = 'cancelado' WHERE id = ?");
  $update->execute([$id_pedido]);
  $pedido['estado'] = 'cancelado';
  echo "<script>alert('Pedido cancelado con éxito.');location.href='ver_pedido.php?id=$id_pedido';</script>";
  exit;
}

// Productos del pedido
$detalle = $conn->prepare("
  SELECT dp.*, p.nombre, p.precio
  FROM detalle_pedido dp
  JOIN productos p ON dp.producto_id = p.id
  WHERE dp.pedido_id = ?
");
$detalle->execute([$id_pedido]);
$productos = $detalle->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container py-5">
  <h1 class="text-center mb-4">🧾 Detalle del Pedido</h1>

  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <p><strong>Pedido N.º:</strong> <?= $pedido['id'] ?></p>
      <p><strong>Fecha:</strong> <?= $pedido['fecha'] ?></p>
      <p><strong>Método de pago:</strong> <?= ucfirst($pedido['metodo_pago']) ?></p>
      <p><strong>Estado:</strong> <span class="badge bg-secondary"><?= ucfirst($pedido['estado']) ?></span></p>
      <p><strong>Total:</strong> $<?= number_format($pedido['total'], 0, ',', '.') ?></p>

      <?php if (!$es_admin && $pedido['estado'] === 'pendiente'): ?>
        <form method="post" onsubmit="return confirm('¿Estás seguro de cancelar este pedido?');">
          <button type="submit" name="cancelar" class="btn btn-danger mt-3">Cancelar pedido</button>
        </form>
      <?php endif; ?>
    </div>
  </div>

  <?php if ($pedido['metodo_pago'] === 'entrega'): ?>
    <div class="card shadow-sm mb-4">
      <div class="card-header fw-bold">📦 Información de envío</div>
      <div class="card-body">
        <p><strong>Nombre:</strong> <?= htmlspecialchars($pedido['nombre_envio'] ?? 'No registrado') ?></p>
        <p><strong>Teléfono:</strong> <?= htmlspecialchars($pedido['telefono_envio'] ?? 'No registrado') ?></p>
        <p><strong>Dirección:</strong> <?= htmlspecialchars($pedido['direccion_envio'] ?? 'No registrada') ?></p>
        <p><strong>Barrio:</strong> <?= htmlspecialchars($pedido['barrio_envio'] ?? 'No registrado') ?></p>
        <p><strong>Ciudad:</strong> <?= htmlspecialchars($pedido['ciudad_envio'] ?? 'No registrada') ?></p>
      </div>
    </div>
  <?php endif; ?>

  <h4 class="mb-3">🛒 Productos del pedido</h4>
  <div class="table-responsive">
    <table class="table table-bordered table-hover text-center align-middle">
      <thead class="table-light">
        <tr>
          <th>Producto</th>
          <th>Precio</th>
          <th>Cantidad</th>
          <th>Subtotal</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($productos as $p): ?>
        <tr>
          <td><?= htmlspecialchars($p['nombre']) ?></td>
          <td>$<?= number_format($p['precio'], 0, ',', '.') ?></td>
          <td><?= $p['cantidad'] ?></td>
          <td>$<?= number_format($p['precio'] * $p['cantidad'], 0, ',', '.') ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="text-center mt-4 d-grid gap-3">
  <a href="<?= $es_admin ? 'admin/pedidos.php' : 'perfil.php' ?>" class="btn btn-secondary btn-lg">← Volver</a>

  <?php if (!$es_admin): ?>
    <button class="btn btn-outline-success btn-lg" onclick="repetirPedido()">🔁 Repetir pedido</button>
  <?php endif; ?>

  <a href="factura_pdf.php?id=<?= $pedido['id'] ?>" target="_blank" class="btn btn-primary btn-lg">
    📄 Descargar factura
  </a>
</div>

</div>

<?php if (!$es_admin): ?>
<script>
function repetirPedido() {
  const productos = <?= json_encode($productos) ?>;
  let carrito = JSON.parse(localStorage.getItem("carrito")) || [];

  productos.forEach(p => {
    for (let i = 0; i < p.cantidad; i++) {
      carrito.push({
        id: p.producto_id,
        nombre: p.nombre,
        precio: p.precio,
        cantidad: 1
      });
    }
  });

  localStorage.setItem("carrito", JSON.stringify(carrito));
  alert("Productos agregados al carrito.");
  window.location.href = "carrito.php";
}
</script>
<?php endif; ?>

<?php include 'footer.php'; ?>
