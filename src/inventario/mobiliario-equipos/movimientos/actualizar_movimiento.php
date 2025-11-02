<?php
error_reporting(E_ALL);
include_once("../../../db/conexion.php");

if(
    !isset(
        $_GET["id"],
        $_GET["activo_id"],
        $_GET["sucursal_id"],
        $_GET["tipo_movimiento"]
    )
) {
    echo "Faltan parámetros";
    exit;
}

$id = intval($_GET["id"]);
$activo_id = intval($_GET["activo_id"]);
$sucursal_id = intval($_GET["sucursal_id"]);
$tipo_movimiento = trim($_GET["tipo_movimiento"]);
$origen = trim($_GET["origen"] ?? "");
$destino = trim($_GET["destino"] ?? "");
$observaciones = trim($_GET["observaciones"] ?? "");

if ($id <= 0 || $activo_id <= 0 || $sucursal_id <= 0) {
    echo "Datos inválidos";
    exit;
}

$db = conectar();

$sql = "
    UPDATE movimientos_activos 
    SET activo_id = ?, sucursal_id = ?, tipo_movimiento = ?, origen = ?, destino = ?, observaciones = ?
    WHERE id = ?
";
$stmt = $db->prepare($sql);
$stmt->bind_param("iissssi", $activo_id, $sucursal_id, $tipo_movimiento, $origen, $destino, $observaciones, $id);

if ($stmt->execute()) echo "Ok";
else echo "Error al actualizar: " . $stmt->error;

$stmt->close();
$db->close();
?>
