<?php
error_reporting(E_ALL);
include_once("../../../db/conexion.php");

if(!isset($_GET["id"], $_GET["nombre"])) {
    echo "Faltan parámetros";
    exit;
}

$id = intval($_GET["id"]);
$nombre = trim($_GET["nombre"]);
$descripcion = trim($_GET["descripcion"] ?? "");

if ($id <= 0) {
    echo "ID inválido";
    exit;
}

if ($nombre === "") {
    echo "El nombre es obligatorio";
    exit;
}

$db = conectar();

// Verificar duplicado
$check = $db->prepare("SELECT id FROM categorias_activos WHERE nombre = ? AND id <> ?");
$check->bind_param("si", $nombre, $id);
$check->execute();
$check->store_result();
if ($check->num_rows > 0) {
    echo "La categoría ya existe";
    $check->close();
    $db->close();
    exit;
}
$check->close();

$sql = "UPDATE categorias_activos SET nombre = ?, descripcion = ? WHERE id = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("ssi", $nombre, $descripcion, $id);

if ($stmt->execute()) echo "Ok";
else echo "Error al actualizar: " . $stmt->error;

$stmt->close();
$db->close();
?>
