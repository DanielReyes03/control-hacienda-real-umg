<?php
error_reporting(E_ALL);
include_once("../../db/conexion.php");
header('Content-Type: application/json');

$db = conectar();

$sql = "
    SELECT 
        i.id,
        i.nombre,
        i.unidad,
        i.ancho,
        i.alto,
        i.largo,
        i.costo,
        i.stock,
        i.stock_minimo,
        i.creado_en,
        s.id AS sucursal_id,
        s.nombre AS sucursal
    FROM inventario_materias_primas i
    INNER JOIN sucursales s ON i.sucursal_id = s.id
    ORDER BY i.nombre ASC
";

$stmt = $db->prepare($sql);

if (!$stmt) {
    echo json_encode([]);
    exit;
}

$stmt->execute();
$result = $stmt->get_result();

$inventario = [];

if ($result) {
    while($row = $result->fetch_assoc()){
        $inventario[] = $row;
    }
}

echo json_encode($inventario);

$stmt->close();
$db->close();
?>
