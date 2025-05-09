<?php
session_start();
require('includes/fpdf/fpdf.php');
include 'includes/conexion.php';

// Validar acceso
if (!isset($_SESSION['usuario']) || !isset($_GET['id'])) {
  die("Acceso denegado.");
}

$id_pedido = $_GET['id'];
$usuario_id = $_SESSION['usuario']['id'];
$es_admin = $_SESSION['usuario']['rol'] === 'admin';

// Obtener el pedido y usuario
if ($es_admin) {
  $stmt = $conn->prepare("SELECT p.*, u.nombre, u.email FROM pedidos p JOIN usuarios u ON p.usuario_id = u.id WHERE p.id = ?");
  $stmt->execute([$id_pedido]);
} else {
  $stmt = $conn->prepare("SELECT p.*, u.nombre, u.email FROM pedidos p JOIN usuarios u ON p.usuario_id = u.id WHERE p.id = ? AND p.usuario_id = ?");
  $stmt->execute([$id_pedido, $usuario_id]);
}
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pedido) {
  die("Pedido no encontrado.");
}

// Productos del pedido
$detalle = $conn->prepare("
  SELECT dp.*, p.nombre AS nombre_producto
  FROM detalle_pedido dp
  JOIN productos p ON dp.producto_id = p.id
  WHERE dp.pedido_id = ?
");
$detalle->execute([$id_pedido]);
$productos = $detalle->fetchAll(PDO::FETCH_ASSOC);

// Crear PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

// Logo
if (file_exists('assets/img/logo.png')) {
  $pdf->Image('assets/img/logo.png', 10, 10, 40);
}
$pdf->Cell(0, 10, '', 0, 1);
$pdf->Ln(15);

// Título
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, utf8_decode('Factura de Pedido'), 0, 1, 'C');
$pdf->Ln(5);

// Info del cliente
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, utf8_decode('Cliente: ') . utf8_decode($pedido['nombre']), 0, 1);
$pdf->Cell(0, 8, 'Correo: ' . $pedido['email'], 0, 1);
$pdf->Cell(0, 8, 'Pedido N°: ' . $pedido['id'], 0, 1);
$pdf->Cell(0, 8, 'Fecha: ' . $pedido['fecha'], 0, 1);
$pdf->Cell(0, 8, 'Estado: ' . ucfirst($pedido['estado']), 0, 1);
$pdf->Cell(0, 8, utf8_decode('Método de pago: ') . ucfirst($pedido['metodo_pago']), 0, 1);

// Info de envío si es pago contra entrega
if ($pedido['metodo_pago'] === 'entrega') {
  $pdf->Ln(5);
  $pdf->SetFont('Arial', 'B', 12);
  $pdf->Cell(0, 8, utf8_decode('Información de Envío'), 0, 1);
  $pdf->SetFont('Arial', '', 12);
  $pdf->Cell(0, 8, utf8_decode('Nombre: ') . utf8_decode($pedido['nombre_envio'] ?? 'No registrado'), 0, 1);
  $pdf->Cell(0, 8, utf8_decode('Teléfono: ') . utf8_decode($pedido['telefono_envio'] ?? 'No registrado'), 0, 1);
  $pdf->Cell(0, 8, utf8_decode('Dirección: ') . utf8_decode($pedido['direccion_envio'] ?? 'No registrada'), 0, 1);
  $pdf->Cell(0, 8, utf8_decode('Barrio: ') . utf8_decode($pedido['barrio_envio'] ?? 'No registrado'), 0, 1);
  $pdf->Cell(0, 8, utf8_decode('Ciudad: ') . utf8_decode($pedido['ciudad_envio'] ?? 'No registrada'), 0, 1);
}

$pdf->Ln(10);

// Tabla de productos
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(230, 0, 115);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(80, 10, 'Producto', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Precio', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Cantidad', 1, 0, 'C', true);
$pdf->Cell(50, 10, 'Subtotal', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 12);
$pdf->SetTextColor(0,0,0);
$total = 0;

foreach ($productos as $p) {
  $subtotal = $p['precio_unitario'] * $p['cantidad'];
  $total += $subtotal;
  $pdf->Cell(80, 10, utf8_decode($p['nombre_producto']), 1);
  $pdf->Cell(30, 10, '$' . number_format($p['precio_unitario'], 0, ',', '.'), 1, 0, 'C');
  $pdf->Cell(30, 10, $p['cantidad'], 1, 0, 'C');
  $pdf->Cell(50, 10, '$' . number_format($subtotal, 0, ',', '.'), 1, 1, 'C');
}

// Total
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(140, 10, 'Total:', 1);
$pdf->Cell(50, 10, '$' . number_format($total, 0, ',', '.'), 1, 1, 'C');

// Pie de página
$pdf->Ln(10);
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 10, utf8_decode("Gracias por tu compra en nuestra tienda de maquillaje 💄"), 0, 1, 'C');

$pdf->Output('I', 'factura_pedido_' . $pedido['id'] . '.pdf');
