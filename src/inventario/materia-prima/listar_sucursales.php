<?php
error_reporting(E_ALL);
include_once("../../db/conexion.php");
header('Content-Type: application/json');

$db = conectar();

// Consulta para obtener solo id y nombre de las sucursales
$sql = "
    SELECT 
        id,
        nombre
    FROM sucursales
    ORDER BY nombre ASC
";

$stmt = $db->prepare($sql);

if (!$stmt) {
    echo json_encode([]);
    exit;
}

$stmt->execute();
$result = $stmt->get_result();

$sucursales = [];

if ($result) {
    while($row = $result->fetch_assoc()){
        $sucursales[] = $row;
    }
}

echo json_encode($sucursales);

$stmt->close();
$db->close();
?>
