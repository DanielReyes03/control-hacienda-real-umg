<?php
error_reporting(E_ALL);
include_once("../../db/conexion.php");
header('Content-Type: application/json');

$db = conectar();

$sql = "
    SELECT 
        a.id,
        a.codigo_interno,
        a.nombre,
        a.descripcion,
        a.marca,
        a.modelo,
        a.serie,
        a.fecha_adquisicion,
        a.costo,
        a.estado,
        c.id AS categoria_id,
        c.nombre AS categoria,
        s.id AS sucursal_id,
        s.nombre AS sucursal
    FROM activos a
    LEFT JOIN categorias_activos c ON a.categoria_id = c.id
    LEFT JOIN sucursales s ON a.sucursal_id = s.id
    ORDER BY a.nombre ASC
";

$stmt = $db->prepare($sql);

if (!$stmt) {
    echo json_encode([]);
    exit;
}

$stmt->execute();
$result = $stmt->get_result();

$activos = [];

if ($result) {
    while($row = $result->fetch_assoc()){
        $activos[] = $row;
    }
}

echo json_encode($activos);

$stmt->close();
$db->close();
?>
