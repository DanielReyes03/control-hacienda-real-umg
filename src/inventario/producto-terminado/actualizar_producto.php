<?php
error_reporting(E_ALL);
include_once("../../db/conexion.php");

if(
    !isset(
        $_GET["id"],
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

$id = intval($_GET["id"]);
$categoria_id = intval($_GET["categoria_id"]);
$sku = trim($_GET["sku"]);
$nombre = trim($_GET["nombre"]);
$descripcion = trim($_GET["descripcion"]);
$precio = floatval($_GET["precio"]);
$es_item_menu = ($_GET["es_item_menu"] === "false" || $_GET["es_item_menu"] === "0") ? 0 : 1;
$receta_id = intval($_GET["receta_id"]);

if ($id <= 0 || $categoria_id <= 0 || $receta_id <= 0) {
    echo "Datos inválidos";
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

$db = conectar();

// Verificar SKU duplicado
$check = $db->prepare("SELECT id FROM productos WHERE sku = ? AND id <> ?");
$check->bind_param("si", $sku, $id);
$check->execute();
$check->store_result();
if ($check->num_rows > 0) {
    echo "El SKU ya existe en otro producto";
    $check->close();
    $db->close();
    exit;
}
$check->close();

$sql = "
    UPDATE productos 
    SET categoria_id = ?, sku = ?, nombre = ?, descripcion = ?, precio = ?, es_item_menu = ?, receta_id = ?
    WHERE id = ?
";
$stmt = $db->prepare($sql);
$stmt->bind_param("isssdiii", $categoria_id, $sku, $nombre, $descripcion, $precio, $es_item_menu, $receta_id, $id);

if ($stmt->execute()) echo "Ok";
else echo "Error al actualizar: " . $stmt->error;

$stmt->close();
$db->close();
?>
