<?php
// api_favoritos.php
header('Content-Type: application/json; charset=utf-8');

try {
    require_once __DIR__ . '/includes/conexion.php';

    // Obtener y validar los IDs
    $idsParam = $_GET['ids'] ?? '';
    if (empty($idsParam)) {
        echo json_encode([]);
        exit;
    }

    $ids = array_filter(array_unique(array_map('intval', explode(',', $idsParam))));
    if (empty($ids)) {
        echo json_encode([]);
        exit;
    }

    // Crear placeholders
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    // Query optimizada
    $sql = "SELECT id, nombre, precio, imagen FROM productos WHERE id IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    $stmt->execute($ids);

    // Convertir resultados en array asociativo indexado por ID
    $result = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $result[$row['id']] = $row;
    }

    // Reordenar los resultados según el orden original de $ids
    $ordenado = [];
    foreach ($ids as $id) {
        if (isset($result[$id])) {
            $ordenado[] = $result[$id];
        }
    }

    echo json_encode($ordenado);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error interno al obtener favoritos',
        'message' => $e->getMessage()
    ]);
}
