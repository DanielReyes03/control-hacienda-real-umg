<?php
error_reporting(E_ALL);
include_once("../../db/conexion.php");

if(
    !isset(
        $_GET["id"],
        $_GET["categoria_id"],
        $_GET["sucursal_id"],
        $_GET["codigo_interno"],
        $_GET["nombre"],
        $_GET["marca"],
        $_GET["modelo"],
        $_GET["serie"],
        $_GET["costo"],
        $_GET["estado"]
    )
) {
    echo "Faltan parámetros";
    exit;
}

$id = intval($_GET["id"]);
$categoria_id = intval($_GET["categoria_id"]);
$sucursal_id = intval($_GET["sucursal_id"]);
$codigo_interno = trim($_GET["codigo_interno"]);
$nombre = trim($_GET["nombre"]);
$marca = trim($_GET["marca"]);
$modelo = trim($_GET["modelo"]);
$serie = trim($_GET["serie"]);
$costo = floatval($_GET["costo"]);
$fecha_adquisicion = $_GET["fecha_adquisicion"] ?? null;
$estado = trim($_GET["estado"]);
$descripcion = $_GET["descripcion"] ?? "";

if ($id <= 0 || $categoria_id <= 0 || $sucursal_id <= 0) {
    echo "Datos inválidos";
    exit;
}

$db = conectar();

// 1️⃣ Obtener datos actuales
$sqlActual = "SELECT sucursal_id, estado, costo FROM activos WHERE id = ?";
$stmtActual = $db->prepare($sqlActual);
$stmtActual->bind_param("i", $id);
$stmtActual->execute();
$resActual = $stmtActual->get_result();
$activoActual = $resActual->fetch_assoc();
$stmtActual->close();

$movimientoNecesario = false;
$tipo_movimiento = "ajuste";
$observaciones = "Ajuste general del activo";

// Detectar si hubo cambios relevantes
if ($activoActual) {
    if ($activoActual["sucursal_id"] != $sucursal_id) {
        $tipo_movimiento = "traslado";
        $observaciones = "Traslado manual del activo";
        $movimientoNecesario = true;
    } elseif ($activoActual["estado"] != $estado) {
        $tipo_movimiento = ($estado == "baja") ? "baja" : "alta";
        $observaciones = "Cambio de estado del activo";
        $movimientoNecesario = true;
    } elseif (abs($activoActual["costo"] - $costo) > 0.01) {
        $tipo_movimiento = "ajuste";
        $observaciones = "Ajuste de costo";
        $movimientoNecesario = true;
    }
}

// Validar formato de fecha
if (empty($fecha_adquisicion) || !preg_match("/^\d{4}-\d{2}-\d{2}$/", $fecha_adquisicion)) {
    $fecha_adquisicion = null;
}

// 2️⃣ Actualizar activo
$sql = "
    UPDATE activos 
    SET categoria_id = ?, sucursal_id = ?, codigo_interno = ?, nombre = ?, descripcion = ?, 
        marca = ?, modelo = ?, serie = ?, fecha_adquisicion = ?, costo = ?, estado = ?
    WHERE id = ?
";

$stmt = $db->prepare($sql);
$stmt->bind_param(
    "iisssssssds" . "i",
    $categoria_id,
    $sucursal_id,
    $codigo_interno,
    $nombre,
    $descripcion,
    $marca,
    $modelo,
    $serie,
    $fecha_adquisicion,
    $costo,
    $estado,
    $id
);

$exito = $stmt->execute();
$stmt->close();

// 3️⃣ Registrar movimiento si corresponde
if ($exito && $movimientoNecesario) {
    $sqlMov = "
        INSERT INTO movimientos_activos 
        (activo_id, origen_id, destino_id, tipo_movimiento, observaciones, fecha_movimiento)
        VALUES (?, ?, ?, ?, ?, NOW())
    ";
    $stmtMov = $db->prepare($sqlMov);
    $origen_id = $activoActual["sucursal_id"] ?? $sucursal_id;
    $stmtMov->bind_param("iiiss", $id, $origen_id, $sucursal_id, $tipo_movimiento, $observaciones);
    $stmtMov->execute();
    $stmtMov->close();
}

if ($exito) {
    echo "Ok";
} else {
    echo "Error al actualizar";
}

$db->close();
?>
