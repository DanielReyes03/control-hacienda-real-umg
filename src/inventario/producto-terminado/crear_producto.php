<?php
error_reporting(E_ALL);
include_once("../../db/conexion.php");

// Validar parámetros requeridos
if(
    !isset(
        $_GET["categoria_id"],
        $_GET["sku"],
        $_GET["nombre"],
        $_GET["descripcion"],
        $_GET["precio"],
        $_GET["es_item_menu"],
        $_GET["receta_id"]
    )
) {
    echo "Faltan parámetros";
    exit;
}

// Sanitizar entradas
$categoria_id = intval($_GET["categoria_id"]);
$sku = trim($_GET["sku"]);
$nombre = trim($_GET["nombre"]);
$descripcion = trim($_GET["descripcion"]);
$precio = floatval($_GET["precio"]);
$es_item_menu = ($_GET["es_item_menu"] === "false" || $_GET["es_item_menu"] === "0") ? 0 : 1;
$receta_id = intval($_GET["receta_id"]);

// Validaciones básicas
if ($categoria_id <= 0) {
    echo "Categoría inválida";
    exit;
}

if (!preg_match("/^[A-Za-z0-9\-\_]+$/", $sku)) {
    echo "SKU inválido";
    exit;
}

if (!preg_match("/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\-\(\)\.]+$/", $nombre)) {
    echo "Nombre inválido";
    exit;
}

if ($precio < 0) {
    echo "Precio inválido";
    exit;
}

if ($receta_id <= 0) {
    echo "Receta inválida";
    exit;
}

// Conexión a la base de datos
$db = conectar();

// Verificar SKU único
$checkSql = "SELECT id FROM productos WHERE sku = ?";
$checkStmt = $db->prepare($checkSql);
$checkStmt->bind_param("s", $sku);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    echo "El SKU ya existe en otro producto";
    $checkStmt->close();
    $db->close();
    exit;
}
$checkStmt->close();

// Insertar nuevo producto
$sql = "
    INSERT INTO productos 
    (categoria_id, sku, nombre, descripcion, precio, es_item_menu, receta_id, creado_en)
    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
";
$stmt = $db->prepare($sql);

if ($stmt === false) {
    echo "Error en la preparación: " . $db->error;
    exit;
}

$stmt->bind_param(
    "isssdii",
    $categoria_id,
    $sku,
    $nombre,
    $descripcion,
    $precio,
    $es_item_menu,
    $receta_id
);

if ($stmt->execute()) {
    echo "Ok";
} else {
    echo "Error al guardar: " . $stmt->error;
}

$stmt->close();
$db->close();
?>
