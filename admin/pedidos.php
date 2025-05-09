<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}

include '../includes/conexion.php';

// Cambiar estado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pedido_id'], $_POST['nuevo_estado'])) {
  $id = $_POST['pedido_id'];
  $nuevo_estado = $_POST['nuevo_estado'];

  $update = $conn->prepare("UPDATE pedidos SET estado = ? WHERE id = ?");
  $update->execute([$nuevo_estado, $id]);

  header("Location: pedidos.php");
  exit;
}

// Obtener pedidos
$pedidos = $conn->query("
  SELECT p.*, u.nombre 
  FROM pedidos p 
  LEFT JOIN usuarios u ON p.usuario_id = u.id 
  ORDER BY p.fecha DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Admin - Pedidos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .estado-pendiente { background: #fff6cc; }
    .estado-aprobado  { background: #d9ecff; }
    .estado-enviado   { background: #ffe5cc; }
    .estado-entregado { background: #d4fcd4; }
    .estado-cancelado { background: #ffd6d6; }
  </style>
</head>
<body>
  <nav class="navbar navbar-dark bg-dark px-4">
    <span class="navbar-brand mb-0 h1">📦 Pedidos Realizados</span>
    <a href="index.php" class="btn btn-outline-light btn-sm">← Volver al panel</a>
  </nav>

  <div class="container py-4">
    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle text-center">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Usuario</th>
            <th>Total</th>
            <th>Fecha</th>
            <th>Estado</th>
            <th>Ver</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($pedidos as $pedido): ?>
            <tr class="estado-<?= $pedido['estado'] ?>">
              <td><?= $pedido['id'] ?></td>
              <td><?= htmlspecialchars($pedido['nombre'] ?? 'Sin usuario') ?></td>
              <td>$<?= number_format($pedido['total'], 0, ',', '.') ?></td>
              <td><?= $pedido['fecha'] ?></td>
              <td>
                <form method="post">
                  <input type="hidden" name="pedido_id" value="<?= $pedido['id'] ?>">
                  <select name="nuevo_estado" class="form-select form-select-sm" onchange="this.form.submit()">
                    <?php
                    $estados = ['pendiente', 'aprobado', 'enviado', 'entregado', 'cancelado'];
                    foreach ($estados as $estado):
                    ?>
                      <option value="<?= $estado ?>" <?= $estado === $pedido['estado'] ? 'selected' : '' ?>>
                        <?= ucfirst($estado) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </form>
              </td>
              <td><a href="../ver_pedido.php?id=<?= $pedido['id'] ?>" class="btn btn-primary btn-sm">Ver</a></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
