<?php
error_reporting(E_ALL);
include_once("../../db/conexion.php");

// Validar parámetros requeridos
if(
    !isset(
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

// Sanitizar entradas
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

// Verificar si ya existe una materia prima con el mismo nombre y sucursal
$checkSql = "SELECT id FROM inventario_materias_primas WHERE nombre = ? AND sucursal_id = ?";
$checkStmt = $db->prepare($checkSql);
$checkStmt->bind_param("si", $nombre, $sucursal_id);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    echo "Ya existe una materia prima con ese nombre en esta sucursal";
    $checkStmt->close();
    $db->close();
    exit;
}
$checkStmt->close();

// Insertar nueva materia prima
$sql = "
    INSERT INTO inventario_materias_primas 
    (nombre, unidad, ancho, alto, largo, costo, stock, stock_minimo, sucursal_id, creado_en) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
";
$stmt = $db->prepare($sql);

if ($stmt === false) {
    echo "Error en la preparación: " . $db->error;
    exit;
}

$stmt->bind_param(
    "ssddddddi",
    $nombre,
    $unidad,
    $ancho,
    $alto,
    $largo,
    $costo,
    $stock,
    $stock_minimo,
    $sucursal_id
);

if ($stmt->execute()) {
    echo "Ok";
} else {
    echo "Error al guardar: " . $stmt->error;
}

$stmt->close();
$db->close();
?>
