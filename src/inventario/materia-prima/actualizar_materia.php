<?php
error_reporting(E_ALL);
include_once("../../db/conexion.php");

// Validar parámetros requeridos
if(
    !isset(
        $_GET["id"],
        $_GET["nombre"], 
        $_GET["unidad"], 
        $_GET["ancho"], 
        $_GET["alto"], 
        $_GET["largo"], 
        $_GET["costo"], 
        $_GET["stock"], 
        $_GET["stock_minimo"], 
        $_GET["sucursal_id"]
    )
) {
    echo "Faltan parámetros";
    exit;
}

// Sanitizar y convertir tipos
$id = intval($_GET["id"]);
$nombre = trim($_GET["nombre"]);
$unidad = trim($_GET["unidad"]);
$ancho = floatval($_GET["ancho"]);
$alto = floatval($_GET["alto"]);
$largo = floatval($_GET["largo"]);
$costo = floatval($_GET["costo"]);
$stock = floatval($_GET["stock"]);
$stock_minimo = floatval($_GET["stock_minimo"]);
$sucursal_id = intval($_GET["sucursal_id"]);

// Validaciones básicas
if ($id <= 0) {
    echo "ID inválido";
    exit;
}

if (!preg_match("/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\-\(\)]+$/", $nombre)) {
    echo "Nombre inválido";
    exit;
}

if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\%\/]*$/", $unidad)) {
    echo "Unidad inválida";
    exit;
}

if ($sucursal_id <= 0) {
    echo "Sucursal inválida";
    exit;
}

// Conexión a la base de datos
$db = conectar();

// Verificar si existe la materia prima
$checkSql = "SELECT id FROM inventario_materias_primas WHERE id = ?";
$checkStmt = $db->prepare($checkSql);
$checkStmt->bind_param("i", $id);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows === 0) {
    echo "La materia prima no existe";
    $checkStmt->close();
    $db->close();
    exit;
}
$checkStmt->close();

// Verificar si ya existe otra materia prima con el mismo nombre en la misma sucursal
$dupSql = "SELECT id FROM inventario_materias_primas WHERE nombre = ? AND sucursal_id = ? AND id != ?";
$dupStmt = $db->prepare($dupSql);
$dupStmt->bind_param("sii", $nombre, $sucursal_id, $id);
$dupStmt->execute();
$dupStmt->store_result();

if ($dupStmt->num_rows > 0) {
    echo "Ya existe una materia prima con ese nombre en esta sucursal";
    $dupStmt->close();
    $db->close();
    exit;
}
$dupStmt->close();

// Actualizar registro
$sql = "
    UPDATE inventario_materias_primas
    SET nombre = ?, unidad = ?, ancho = ?, alto = ?, largo = ?, costo = ?, stock = ?, stock_minimo = ?, sucursal_id = ?
    WHERE id = ?
";
$stmt = $db->prepare($sql);

if ($stmt === false) {
    echo "Error en la preparación: " . $db->error;
    exit;
}

$stmt->bind_param(
    "ssddddddii",
    $nombre,
    $unidad,
    $ancho,
    $alto,
    $largo,
    $costo,
    $stock,
    $stock_minimo,
    $sucursal_id,
    $id
);

if ($stmt->execute()) {
    echo "Ok";
} else {
    echo "Error al actualizar: " . $stmt->error;
}

$stmt->close();
$db->close();
?>
