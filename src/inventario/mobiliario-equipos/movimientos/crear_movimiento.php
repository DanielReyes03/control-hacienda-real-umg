<?php
error_reporting(E_ALL);
include_once("../../../db/conexion.php");

// Validar parámetros requeridos
if (!isset($_GET["activo_id"], $_GET["tipo_movimiento"])) {
    echo "Faltan parámetros";
    exit;
}

$activo_id = intval($_GET["activo_id"]);
$tipo_movimiento = trim($_GET["tipo_movimiento"]);
$destino_id = isset($_GET["destino_id"]) && $_GET["destino_id"] !== "" ? intval($_GET["destino_id"]) : null;
$observaciones = trim($_GET["observaciones"] ?? "");

if ($activo_id <= 0) {
    echo "Activo inválido";
    exit;
}

if ($tipo_movimiento === "") {
    echo "Tipo de movimiento obligatorio";
    exit;
}

$db = conectar();

// Obtener sucursal origen
$sqlOrigen = "SELECT sucursal_id FROM activos WHERE id = ?";
$stmtOrigen = $db->prepare($sqlOrigen);
$stmtOrigen->bind_param("i", $activo_id);
$stmtOrigen->execute();
$resOrigen = $stmtOrigen->get_result();
$rowOrigen = $resOrigen->fetch_assoc();
$origen_id = $rowOrigen ? intval($rowOrigen["sucursal_id"]) : null;
$stmtOrigen->close();

// Si el tipo no requiere destino, lo anulamos
if (in_array($tipo_movimiento, ["baja", "mantenimiento", "alta"])) {
    $destino_id = null;
}

// Insertar movimiento
$sql = "
    INSERT INTO movimientos_activos 
    (activo_id, origen_id, destino_id, tipo_movimiento, observaciones, fecha_movimiento)
    VALUES (?, ?, ?, ?, ?, NOW())
";
$stmt = $db->prepare($sql);
$stmt->bind_param("iiiss", $activo_id, $origen_id, $destino_id, $tipo_movimiento, $observaciones);

if ($stmt->execute()) {
    echo "Ok";

    // Si es traslado o asignación → cambia sucursal
    if (in_array($tipo_movimiento, ["traslado", "asignacion"]) && $destino_id) {
        $upd = $db->prepare("UPDATE activos SET sucursal_id = ? WHERE id = ?");
        $upd->bind_param("ii", $destino_id, $activo_id);
        $upd->execute();
        $upd->close();
    }

    // Si es baja → marcar inactivo
    if ($tipo_movimiento === "baja") {
        $upd = $db->prepare("UPDATE activos SET estado = 'baja' WHERE id = ?");
        $upd->bind_param("i", $activo_id);
        $upd->execute();
        $upd->close();
    }

    // Si es alta → marcar activo
    if ($tipo_movimiento === "alta") {
        $upd = $db->prepare("UPDATE activos SET estado = 'activo' WHERE id = ?");
        $upd->bind_param("i", $activo_id);
        $upd->execute();
        $upd->close();
    }

} else {
    echo "Error al guardar: " . $stmt->error;
}

$stmt->close();
$db->close();
?>
