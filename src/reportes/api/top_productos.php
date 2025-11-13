<?php
header('Content-Type: application/json');

try {
    // Conexión a la base
    $pdo = new PDO(
        "mysql:host=db;dbname=mydb;charset=utf8mb4",
        "user",
        "userpassword",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    // Parámetros GET (mes y año actuales si no se pasan)
    $anio = isset($_GET['anio']) ? (int)$_GET['anio'] : (int)date('Y');
    $mes  = isset($_GET['mes'])  ? (int)$_GET['mes']  : (int)date('n');

    // Consulta: Top 5 productos más vendidos por cantidad
    $sql = "
        SELECT 
            p.nombre AS producto,
            SUM(vd.cantidad) AS total_vendido
        FROM ventas_detalle vd
        INNER JOIN productos p ON p.id = vd.producto_id
        INNER INNER JOIN ventas v ON v.id = vd.venta_id
        WHERE v.estado = 'cerrada'
          AND YEAR(v.fecha_venta) = :anio
          AND MONTH(v.fecha_venta) = :mes
        GROUP BY p.id, p.nombre
        ORDER BY total_vendido DESC
        LIMIT 5
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'anio' => $anio,
        'mes'  => $mes
    ]);

    echo json_encode($stmt->fetchAll());

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
