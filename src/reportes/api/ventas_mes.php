<?php
header('Content-Type: application/json');

// Conexión PDO
$pdo = new PDO(
    "mysql:host=db;dbname=mydb;charset=utf8mb4",
    "user",
    "userpassword",
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]
);

// Parámetros GET (por defecto, el mes y año actuales)
$anio = isset($_GET['anio']) ? (int)$_GET['anio'] : (int)date('Y');
$mes  = isset($_GET['mes'])  ? (int)$_GET['mes']  : (int)date('n');

// Consulta SQL: ventas por día del mes seleccionado
$sql = "
    SELECT
        DAY(fecha_venta) AS dia,
        SUM(total)        AS total
    FROM ventas
    WHERE estado = 'cerrada'
      AND YEAR(fecha_venta) = :anio
      AND MONTH(fecha_venta) = :mes
    GROUP BY dia
    ORDER BY dia ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    'anio' => $anio,
    'mes'  => $mes
]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
