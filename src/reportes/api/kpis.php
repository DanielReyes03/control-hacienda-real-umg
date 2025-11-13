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

    // Si vienen por GET, usamos esos; si no, mes/año actual
    $anio = isset($_GET['anio']) ? (int)$_GET['anio'] : (int)date('Y');
    $mes  = isset($_GET['mes'])  ? (int)$_GET['mes']  : (int)date('n');

    // Total de ventas del mes
    $sqlTotal = "
        SELECT SUM(total) AS total_mes
        FROM ventas
        WHERE estado = 'cerrada'
          AND YEAR(fecha_venta) = :anio
          AND MONTH(fecha_venta) = :mes
    ";
    $stmt = $pdo->prepare($sqlTotal);
    $stmt->execute(['anio' => $anio, 'mes' => $mes]);
    $total_mes = (float) ($stmt->fetchColumn() ?? 0);

    // Ticket promedio (promedio de total por venta cerrada del mes)
    $sqlTicket = "
        SELECT AVG(total) AS ticket_promedio
        FROM ventas
        WHERE estado = 'cerrada'
          AND YEAR(fecha_venta) = :anio
          AND MONTH(fecha_venta) = :mes
    ";
    $stmt = $pdo->prepare($sqlTicket);
    $stmt->execute(['anio' => $anio, 'mes' => $mes]);
    $ticket_promedio = (float) ($stmt->fetchColumn() ?? 0);

    // Respuesta JSON
    echo json_encode([
        'total_mes'       => round($total_mes, 2),
        'ticket_promedio' => round($ticket_promedio, 2)
    ]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al calcular KPI']);
}
