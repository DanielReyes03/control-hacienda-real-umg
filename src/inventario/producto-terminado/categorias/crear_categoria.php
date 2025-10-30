<?php
error_reporting(E_ALL);
include_once("../../../db/conexion.php");

// Validar parámetros requeridos
if(
    !isset($_GET["nombre"])
) {
    echo "Faltan parámetros";
    exit;
}

// Sanitizar entradas
$nombre = trim($_GET["nombre"]);
$descripcion = isset($_GET["descripcion"]) ? trim($_GET["descripcion"]) : "";

// Validaciones básicas
if (!preg_match("/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\-\(\)]+$/", $nombre)) {
    echo "Nombre inválido";
    exit;
}

// Conexión a la base de datos
$db = conectar();

// Verificar si ya existe una categoría con el mismo nombre
$checkSql = "SELECT id FROM categorias_productos WHERE nombre = ?";
$checkStmt = $db->prepare($checkSql);
$checkStmt->bind_param("s", $nombre);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    echo "Ya existe una categoría con ese nombre";
    $checkStmt->close();
    $db->close();
    exit;
}
$checkStmt->close();

// Insertar nueva categoría
$sql = "
    INSERT INTO categorias_productos (nombre, descripcion)
    VALUES (?, ?)
";
$stmt = $db->prepare($sql);

if ($stmt === false) {
    echo "Error en la preparación: " . $db->error;
    exit;
}

$stmt->bind_param("ss", $nombre, $descripcion);

if ($stmt->execute()) {
    echo "Ok";
} else {
    echo "Error al guardar: " . $stmt->error;
}

$stmt->close();
$db->close();
?>
