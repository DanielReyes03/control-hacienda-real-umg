<?php
header('Content-Type: application/json');

try {
  $pdo = new PDO(
    "mysql:host=db;dbname=mydb;charset=utf8mb4",
    "user",
    "userpassword",
    [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
  );

  // Parámetros (por defecto: actuales)
  $anio   = isset($_GET['anio']) ? (int)$_GET['anio'] : (int)date('Y');
  $mes    = isset($_GET['mes'])  ? (int)$_GET['mes']  : (int)date('n');
  $estado = 'cerrada'; // fijo, pero podrías aceptar ?estado=

  // Total de ventas por sucursal en el mes/año seleccionados
  $sql = "
    SELECT
      COALESCE(s.nombre, 'Sin sucursal') AS sucursal,
      SUM(v.total) AS total
    FROM ventas v
    LEFT JOIN sucursales s ON s.id = v.sucursal_id
    WHERE v.estado = :estado
      AND YEAR(v.fecha_venta) = :anio
      AND MONTH(v.fecha_venta) = :mes
    GROUP BY sucursal
    ORDER BY total DESC
  ";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    'estado' => $estado,
    'anio'   => $anio,
    'mes'    => $mes
  ]);

  echo json_encode($stmt->fetchAll());
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['error' => 'Error al generar reporte por sucursal']);
}
