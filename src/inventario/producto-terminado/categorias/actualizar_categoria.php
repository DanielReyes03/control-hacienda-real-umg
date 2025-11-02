<?php
error_reporting(E_ALL);
include_once("../../../db/conexion.php");

if (!isset($_GET["id"], $_GET["nombre"])) {
    echo "Faltan parámetros";
    exit;
}

$id = intval($_GET["id"]);
$nombre = trim($_GET["nombre"]);
$descripcion = isset($_GET["descripcion"]) ? trim($_GET["descripcion"]) : "";

if ($id <= 0) {
    echo "ID inválido";
    exit;
}

if (!preg_match("/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\-\(\)]+$/", $nombre)) {
    echo "Nombre inválido";
    exit;
}

$db = conectar();

// Verificar duplicado
$checkSql = "SELECT id FROM categorias_productos WHERE nombre = ? AND id <> ?";
$checkStmt = $db->prepare($checkSql);
$checkStmt->bind_param("si", $nombre, $id);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    echo "Ya existe una categoría con ese nombre";
    $checkStmt->close();
    $db->close();
    exit;
}
$checkStmt->close();

$sql = "UPDATE categorias_productos SET nombre = ?, descripcion = ? WHERE id = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("ssi", $nombre, $descripcion, $id);

if ($stmt->execute()) echo "Ok";
else echo "Error al actualizar: " . $stmt->error;

$stmt->close();
$db->close();
?>
