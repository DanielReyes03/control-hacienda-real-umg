<?php
error_reporting(E_ALL);
include_once("../../../db/conexion.php");

if (!isset($_GET["id"])) {
    echo "Faltan parámetros";
    exit;
}

$id = intval($_GET["id"]);
if ($id <= 0) {
    echo "ID inválido";
    exit;
}

$db = conectar();

// Verificar si hay productos asociados
$check = $db->prepare("SELECT id FROM productos WHERE categoria_id = ?");
$check->bind_param("i", $id);
$check->execute();
$check->store_result();
if ($check->num_rows > 0) {
    echo "No se puede eliminar, hay productos asociados a esta categoría";
    $check->close();
    $db->close();
    exit;
}
$check->close();

$stmt = $db->prepare("DELETE FROM categorias_productos WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) echo "Ok";
else echo "Error al eliminar: " . $stmt->error;

$stmt->close();
$db->close();
?>
