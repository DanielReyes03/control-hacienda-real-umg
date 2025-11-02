<?php
error_reporting(E_ALL);
include_once("../../../db/conexion.php");
header('Content-Type: application/json');

$db = conectar();

$sql = "SELECT id, nombre, descripcion FROM categorias_activos ORDER BY nombre ASC";
$stmt = $db->prepare($sql);

if (!$stmt) {
    echo json_encode([]);
    exit;
}

$stmt->execute();
$result = $stmt->get_result();

$categorias = [];
if ($result) {
    while($row = $result->fetch_assoc()){
        $categorias[] = $row;
    }
}

echo json_encode($categorias);

$stmt->close();
$db->close();
?>
