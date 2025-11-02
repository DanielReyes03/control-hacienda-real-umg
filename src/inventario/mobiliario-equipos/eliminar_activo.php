<?php
error_reporting(E_ALL);
include_once("../../db/conexion.php");

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

// Verificar si el activo existe y su sucursal
$sql = "SELECT sucursal_id, estado FROM activos WHERE id = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$activo = $res->fetch_assoc();
$stmt->close();

if (!$activo) {
    echo "Activo no encontrado";
    $db->close();
    exit;
}

// Si ya estaba de baja, no volver a bajarlo
if ($activo["estado"] === "baja") {
    echo "El activo ya está dado de baja";
    $db->close();
    exit;
}

// Cambiar estado a “baja”
$update = $db->prepare("UPDATE activos SET estado = 'baja' WHERE id = ?");
$update->bind_param("i", $id);
if (!$update->execute()) {
    echo "Error al dar de baja: " . $update->error;
    $update->close();
    $db->close();
    exit;
}
$update->close();

// Registrar movimiento tipo “baja”
$sqlMov = "
    INSERT INTO movimientos_activos 
    (activo_id, origen_id, destino_id, tipo_movimiento, observaciones, fecha_movimiento)
    VALUES (?, ?, NULL, 'baja', 'Baja lógica del activo', NOW())
";
$mov = $db->prepare($sqlMov);
$mov->bind_param("ii", $id, $activo["sucursal_id"]);
$mov->execute();
$mov->close();

echo "Ok";
$db->close();
?>
