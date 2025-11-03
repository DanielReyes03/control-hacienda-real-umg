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

  // Parámetros opcionales
  $sucursalId = isset($_GET['sucursal_id']) ? (int)$_GET['sucursal_id'] : null;
  $limit      = isset($_GET['limit']) ? max(1, (int)$_GET['limit']) : 10; // top 10 más críticos

  $sql = "
    SELECT
      mp.id,
      mp.nombre,
      mp.stock,
      mp.stock_minimo,
      mp.sucursal_id,
      s.nombre AS sucursal,
      (mp.stock_minimo - mp.stock) AS faltante,
      CASE 
        WHEN mp.stock_minimo > 0 THEN (mp.stock / mp.stock_minimo)
        ELSE 1
      END AS ratio
    FROM inventario_materias_primas mp
    INNER JOIN sucursales s ON s.id = mp.sucursal_id
    WHERE mp.stock <= mp.stock_minimo
    /** filtro sucursal **/
    /** order & limit **/
  ";

  // Construir dinámicamente WHERE/ORDER
  $params = [];
  if (!is_null($sucursalId)) {
    $sql = str_replace('/** filtro sucursal **/', 'AND mp.sucursal_id = :sucursalId', $sql);
    $params[':sucursalId'] = $sucursalId;
  } else {
    $sql = str_replace('/** filtro sucursal **/', '', $sql);
  }

  $sql = str_replace('/** order & limit **/', 'ORDER BY ratio ASC, faltante DESC LIMIT :lim', $sql);

  $stmt = $pdo->prepare($sql);

  // Bind params
  foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v, PDO::PARAM_INT);
  }
  $stmt->bindValue(':lim', (int)$limit, PDO::PARAM_INT);

  $stmt->execute();

  echo json_encode($stmt->fetchAll());
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['error' => 'Error al obtener inventario crítico']);
}
