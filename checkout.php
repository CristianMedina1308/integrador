<?php
include 'includes/conexion.php';
require 'includes/fpdf/fpdf.php';
require 'includes/PHPMailer/PHPMailer.php';
require 'includes/PHPMailer/SMTP.php';
require 'includes/PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include 'header.php';

if (!isset($_SESSION['usuario'])) {
  echo '<div class="container py-5 text-center"><p>Debes <a href="login.php">iniciar sesión</a> para finalizar la compra.</p></div>';
  include 'footer.php';
  exit;
}

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['carrito_data'], $_POST['metodo_pago'])) {
  $usuario_id = $_SESSION['usuario']['id'];
  $carrito = json_decode($_POST['carrito_data'], true);
  $metodo_pago = $_POST['metodo_pago'];

  $nombre = $_POST['nombre'] ?? null;
  $telefono = $_POST['telefono'] ?? null;
  $direccion = $_POST['direccion'] ?? null;
  $ciudad = $_POST['ciudad'] ?? null;
  $barrio = $_POST['barrio'] ?? null;

  if ($metodo_pago === 'entrega') {
    if (!$nombre) $errores[] = "El nombre completo es obligatorio.";
    if (!$telefono) $errores[] = "El número de teléfono es obligatorio.";
    if (!$direccion) $errores[] = "La dirección de envío es obligatoria.";
    if (!$barrio) $errores[] = "El barrio es obligatorio.";
    if (!$ciudad) $errores[] = "La ciudad es obligatoria.";
  }

  if (empty($errores)) {
    $total = 0;
    foreach ($carrito as $item) {
      $total += $item['precio'] * ($item['cantidad'] ?? 1);
    }

    $stmt = $conn->prepare("INSERT INTO pedidos (usuario_id, total, estado, metodo_pago, nombre_envio, telefono_envio, direccion_envio, barrio_envio, ciudad_envio) VALUES (?, ?, 'pendiente', ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$usuario_id, $total, $metodo_pago, $nombre, $telefono, $direccion, $barrio, $ciudad]);
    $pedido_id = $conn->lastInsertId();

    $detalle = $conn->prepare("INSERT INTO detalle_pedido (pedido_id, producto_id, nombre_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?, ?)");
    foreach ($carrito as $item) {
      if (!empty($item['id'])) {
        $detalle->execute([$pedido_id, $item['id'], $item['nombre'], $item['cantidad'] ?? 1, $item['precio']]);
      }
    }
  
// Crear factura PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, utf8_decode('Factura de Pedido'), 0, 1, 'C');
$pdf->SetFont('Arial', '', 12);
$pdf->Ln(5);
$pdf->Cell(0, 8, 'Cliente: ' . $_SESSION['usuario']['nombre'], 0, 1);
$pdf->Cell(0, 8, 'Correo: ' . $_SESSION['usuario']['email'], 0, 1);
$pdf->Cell(0, 8, 'Pedido #: ' . $pedido_id, 0, 1);
$pdf->Cell(0, 8, 'Total: $' . number_format($total, 0, ',', '.'), 0, 1);
$pdf->Ln(10);
$pdf->Cell(0, 8, 'Gracias por tu compra 💄', 0, 1, 'C');
$pdfdoc = $pdf->Output('S'); // Salida en memoria

$mail = new PHPMailer(true);
try {
  $mail->isSMTP();
  $mail->Host = 'smtp.gmail.com';      // 👈 CAMBIA ESTO
  $mail->SMTPAuth = true;
  $mail->Username = 'mecristian14@gmail.com'; // 👈 CAMBIA ESTO
  $mail->Password = 'xxxxx';         // 👈 CAMBIA ESTO
  $mail->SMTPSecure = 'tls';
  $mail->Port = 587;

  $mail->setFrom('mecristian14@gmail.com', 'Tienda de Maquillaje');
  $mail->addAddress($_SESSION['usuario']['email'], $_SESSION['usuario']['nombre']);
  $mail->Subject = 'Factura de tu pedido #' . $pedido_id;
  $mail->Body = "Hola " . $_SESSION['usuario']['nombre'] . ",\n\nAdjunto encontrarás la factura de tu pedido.\n\n¡Gracias por tu compra!";
  $mail->addStringAttachment($pdfdoc, 'factura_pedido_' . $pedido_id . '.pdf');

  $mail->send();
} catch (Exception $e) {
  error_log("Error al enviar el correo: " . $mail->ErrorInfo);
}


    echo '
      <div class="container py-5 text-center">
        <div class="alert alert-success shadow-sm p-4 rounded" role="alert">
          <h2 class="mb-3">¡Gracias por tu compra, ' . htmlspecialchars($_SESSION['usuario']['nombre']) . '!</h2>
          <p>Tu pedido ha sido registrado exitosamente con número de pedido <strong>#' . $pedido_id . '</strong>.</p>
          <p>Método de pago: <strong>' . ucfirst($metodo_pago) . '</strong></p>
          <p>Total pagado: <strong>$' . number_format($total, 2, ',', '.') . '</strong></p>
          <p>📧 Se ha enviado una confirmación a tu correo (simulado).</p>
          <a href="productos.php" class="btn btn-primary mt-3">Volver a comprar</a>
        </div>
        <script>localStorage.removeItem("carrito"); localStorage.removeItem("contador");</script>
      </div>';
    include 'footer.php';
    exit;
  }
}
?>

<div class="container py-5">
  <h1 class="mb-4 text-center">🧾 Confirmar Pedido</h1>

  <?php if (!empty($errores)): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach ($errores as $error): ?>
          <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <div id="resumen-pedido"></div>
    </div>
  </div>

  <form method="post" id="formCheckout">
    <input type="hidden" name="carrito_data" id="carrito_data">

    <div class="card shadow-sm mb-4">
      <div class="card-body">
        <h4 class="mb-3">💳 Método de pago</h4>

        <div class="form-check mb-2">
          <input class="form-check-input" type="radio" name="metodo_pago" id="pagoTarjeta" value="tarjeta" required <?= ($_POST['metodo_pago'] ?? '') === 'tarjeta' ? 'checked' : '' ?>>
          <label class="form-check-label" for="pagoTarjeta">Tarjeta de crédito</label>
        </div>

        <div id="formTarjeta" style="display:none;" class="mb-3">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Nombre en la tarjeta</label>
              <input type="text" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Número de tarjeta</label>
              <input type="text" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label">Mes</label>
              <input type="text" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label">Año</label>
              <input type="text" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label">CVV</label>
              <input type="text" class="form-control">
            </div>
          </div>
        </div>

        <div class="form-check mb-2">
          <input class="form-check-input" type="radio" name="metodo_pago" id="pagoEntrega" value="entrega" <?= ($_POST['metodo_pago'] ?? '') === 'entrega' ? 'checked' : '' ?>>
          <label class="form-check-label" for="pagoEntrega">Pago contra entrega</label>
        </div>

        <div id="formEntrega" style="display:none;" class="mb-3">
          <div class="mb-3">
            <label class="form-label">Nombre completo</label>
            <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Teléfono</label>
            <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Dirección</label>
            <input type="text" name="direccion" class="form-control" value="<?= htmlspecialchars($_POST['direccion'] ?? '') ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Barrio</label>
            <input type="text" name="barrio" class="form-control" value="<?= htmlspecialchars($_POST['barrio'] ?? '') ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Ciudad</label>
            <input type="text" name="ciudad" class="form-control" value="<?= htmlspecialchars($_POST['ciudad'] ?? '') ?>">
          </div>
        </div>
      </div>
    </div>

    <div class="text-center mt-4 d-grid gap-3">
  <button type="submit" class="btn btn-success btn-lg">✅ Finalizar Compra</button>
  <a href="carrito.php" class="btn btn-outline-secondary btn-lg">🛒 Volver al Carrito</a>
</div>


<script>
document.addEventListener("DOMContentLoaded", function () {
  const carrito = JSON.parse(localStorage.getItem("carrito")) || [];
  const resumen = document.getElementById("resumen-pedido");
  const hidden = document.getElementById("carrito_data");
  const formTarjeta = document.getElementById("formTarjeta");
  const formEntrega = document.getElementById("formEntrega");

  let total = 0;
  let html = "";

  if (carrito.length === 0) {
    resumen.innerHTML = "<div class='alert alert-warning'>No hay productos en el carrito.</div>";
    document.getElementById("formCheckout").style.display = "none";
    return;
  }

  html += `<div class="table-responsive">
      <table class="table table-bordered text-center align-middle">
        <thead class="table-light">
          <tr>
            <th>Producto</th>
            <th>Precio</th>
            <th>Cantidad</th>
            <th>Subtotal</th>
          </tr>
        </thead>
        <tbody>`;

  carrito.forEach(item => {
    const cantidad = item.cantidad ?? 1;
    const subtotal = item.precio * cantidad;
    total += subtotal;
    html += `<tr>
        <td>${item.nombre}</td>
        <td>$${item.precio.toLocaleString()}</td>
        <td>${cantidad}</td>
        <td>$${subtotal.toLocaleString()}</td>
      </tr>`;
  });

  html += `<tr class="table-secondary">
          <td colspan="3"><strong>Total</strong></td>
          <td><strong>$${total.toLocaleString()}</strong></td>
        </tr>
      </tbody>
    </table>
  </div>`;

  resumen.innerHTML = html;
  hidden.value = JSON.stringify(carrito);

  // Mostrar formularios según el método
  function toggleForms() {
    const metodo = document.querySelector('input[name="metodo_pago"]:checked')?.value;
    formTarjeta.style.display = metodo === 'tarjeta' ? 'block' : 'none';
    formEntrega.style.display = metodo === 'entrega' ? 'block' : 'none';
  }

  document.querySelectorAll('input[name="metodo_pago"]').forEach(input => {
    input.addEventListener('change', toggleForms);
  });

  toggleForms(); // Ejecutar al cargar
});
</script>

<?php include 'footer.php'; ?>
