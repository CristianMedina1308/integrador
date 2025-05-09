<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}

include '../includes/conexion.php';

setlocale(LC_TIME, 'es_ES.UTF-8', 'spanish');
$mes_actual = date('Y-m');

// Ventas y pedidos del mes
$stmt = $conn->prepare("SELECT SUM(total) AS total_ventas, COUNT(*) AS pedidos FROM pedidos WHERE DATE_FORMAT(fecha, '%Y-%m') = ?");
$stmt->execute([$mes_actual]);
$datos = $stmt->fetch(PDO::FETCH_ASSOC);

// Productos más vendidos
$productos = $conn->query("
  SELECT p.nombre, SUM(dp.cantidad) AS total_vendidos
  FROM detalle_pedido dp
  JOIN productos p ON dp.producto_id = p.id
  GROUP BY dp.producto_id
  ORDER BY total_vendidos DESC
  LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// Pedidos por estado
$estados = $conn->query("
  SELECT estado, COUNT(*) as cantidad
  FROM pedidos
  GROUP BY estado
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel de Administración</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    canvas {
      max-width: 500px;
      margin: 0 auto;
    }
  </style>
</head>
<body class="bg-light">
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
    <a class="navbar-brand" href="#">Admin Panel</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
        <li class="nav-item"><a class="nav-link" href="productos.php">Productos</a></li>
        <li class="nav-item"><a class="nav-link" href="usuarios.php">Usuarios</a></li>
        <li class="nav-item"><a class="nav-link" href="pedidos.php">Pedidos</a></li>
        <li class="nav-item"><a class="nav-link" href="../logout.php">Cerrar sesión</a></li>
      </ul>
    </div>
  </nav>

  <div class="container py-5">
    <h1 class="mb-4 text-center">👋 Bienvenido, <?= htmlspecialchars($_SESSION['usuario']['nombre']) ?></h1>

    <div class="row text-center mb-4">
      <div class="col-md-6">
        <div class="card border-primary">
          <div class="card-body">
            <h5 class="card-title">💵 Ventas de <?= strftime('%B %Y') ?></h5>
            <p class="card-text fs-4 text-success">$<?= number_format($datos['total_ventas'] ?? 0, 0, ',', '.') ?></p>
          </div>
        </div>
      </div>
      <div class="col-md-6 mt-4 mt-md-0">
        <div class="card border-info">
          <div class="card-body">
            <h5 class="card-title">📦 Pedidos este mes</h5>
            <p class="card-text fs-4 text-info"><?= $datos['pedidos'] ?></p>
          </div>
        </div>
      </div>
    </div>

    <div class="mb-5">
      <h3>🔥 Productos más vendidos</h3>
      <?php if (count($productos) > 0): ?>
        <div class="table-responsive">
          <table class="table table-bordered table-striped mt-3">
            <thead class="table-light">
              <tr>
                <th>Producto</th>
                <th>Cantidad vendida</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($productos as $p): ?>
                <tr>
                  <td><?= htmlspecialchars($p['nombre']) ?></td>
                  <td><?= $p['total_vendidos'] ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <p class="text-muted">No hay productos vendidos todavía.</p>
      <?php endif; ?>
    </div>

    <div class="row">
      <div class="col-md-6 mb-4 text-center">
        <h5 class="mb-3">📈 Gráfico: Productos más vendidos</h5>
        <canvas id="graficoProductos"></canvas>
      </div>
      <div class="col-md-6 mb-4 text-center">
        <h5 class="mb-3">📊 Gráfico: Pedidos por estado</h5>
        <canvas id="graficoEstados"></canvas>
      </div>
    </div>
  </div>

  <script>
    const productosLabels = <?= json_encode(array_column($productos, 'nombre')) ?>;
    const productosDatos = <?= json_encode(array_column($productos, 'total_vendidos')) ?>;

    const estadosLabels = <?= json_encode(array_column($estados, 'estado')) ?>;
    const estadosDatos = <?= json_encode(array_column($estados, 'cantidad')) ?>;

    new Chart(document.getElementById("graficoProductos"), {
      type: "bar",
      data: {
        labels: productosLabels,
        datasets: [{
          label: "Cantidad vendida",
          data: productosDatos,
          backgroundColor: "rgba(230, 0, 115, 0.6)"
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: false }
        }
      }
    });

    new Chart(document.getElementById("graficoEstados"), {
      type: "pie",
      data: {
        labels: estadosLabels,
        datasets: [{
          data: estadosDatos,
          backgroundColor: [
            "rgba(255, 99, 132, 0.6)",
            "rgba(54, 162, 235, 0.6)",
            "rgba(75, 192, 192, 0.6)",
            "rgba(255, 205, 86, 0.6)"
          ]
        }]
      },
      options: {
        responsive: true
      }
    });
  </script>
</body>
</html>
