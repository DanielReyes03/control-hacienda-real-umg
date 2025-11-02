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

$stmt = $db->prepare("DELETE FROM movimientos_activos WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) echo "Ok";
else echo "Error al eliminar: " . $stmt->error;

$stmt->close();
$db->close();
?>
