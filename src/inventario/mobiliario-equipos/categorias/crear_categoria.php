<?php
error_reporting(E_ALL);
include_once("../../../db/conexion.php");

if(!isset($_GET["nombre"])) {
    echo "Faltan parámetros";
    exit;
}

$nombre = trim($_GET["nombre"]);
$descripcion = trim($_GET["descripcion"] ?? "");

if ($nombre === "") {
    echo "El nombre es obligatorio";
    exit;
}

if (!preg_match("/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\-\(\)\.]+$/", $nombre)) {
    echo "Nombre inválido";
    exit;
}

$db = conectar();

// Verificar duplicado
$check = $db->prepare("SELECT id FROM categorias_activos WHERE nombre = ?");
$check->bind_param("s", $nombre);
$check->execute();
$check->store_result();
if ($check->num_rows > 0) {
    echo "La categoría ya existe";
    $check->close();
    $db->close();
    exit;
}
$check->close();

$sql = "INSERT INTO categorias_activos (nombre, descripcion) VALUES (?, ?)";
$stmt = $db->prepare($sql);
$stmt->bind_param("ss", $nombre, $descripcion);

if ($stmt->execute()) echo "Ok";
else echo "Error al guardar: " . $stmt->error;

$stmt->close();
$db->close();
?>
