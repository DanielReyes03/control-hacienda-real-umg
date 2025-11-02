<?php
error_reporting(E_ALL);
include_once("../../db/conexion.php");

// Validar parámetros requeridos
if(
    !isset(
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

// Sanitizar entradas
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

// Validar formato de fecha
if (empty($fecha_adquisicion) || !preg_match("/^\d{4}-\d{2}-\d{2}$/", $fecha_adquisicion)) {
    $fecha_adquisicion = null;
}

// Validaciones básicas
if ($categoria_id <= 0 || $sucursal_id <= 0) {
    echo "Categoría o sucursal inválida";
    exit;
}

if (!preg_match("/^[A-Za-z0-9\-\_]+$/", $codigo_interno)) {
    echo "Código interno inválido";
    exit;
}

if ($nombre === "") {
    echo "El nombre es obligatorio";
    exit;
}

if ($costo < 0) {
    echo "Costo inválido";
    exit;
}

$db = conectar();

// Verificar código único
$checkSql = "SELECT id FROM activos WHERE codigo_interno = ?";
$checkStmt = $db->prepare($checkSql);
$checkStmt->bind_param("s", $codigo_interno);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    echo "El código interno ya existe";
    $checkStmt->close();
    $db->close();
    exit;
}
$checkStmt->close();

// Insertar activo
$sql = "
    INSERT INTO activos 
    (categoria_id, sucursal_id, codigo_interno, nombre, descripcion, marca, modelo, serie, fecha_adquisicion, costo, estado, creado_en)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
";
$stmt = $db->prepare($sql);

if (!$stmt) {
    echo "Error en la preparación: " . $db->error;
    exit;
}

$stmt->bind_param(
    "iisssssssds",
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
    $estado
);

if ($stmt->execute()) {
    $activo_id = $stmt->insert_id; // 🆔 Obtenemos el ID del nuevo activo

    // Registrar movimiento tipo "alta"
    $movSql = "
        INSERT INTO movimientos_activos 
        (activo_id, origen_id, destino_id, tipo_movimiento, observaciones, fecha_movimiento)
        VALUES (?, NULL, ?, 'alta', 'Alta inicial del activo', NOW())
    ";
    $movStmt = $db->prepare($movSql);
    $movStmt->bind_param("ii", $activo_id, $sucursal_id);
    $movStmt->execute();
    $movStmt->close();

    echo "Ok";
} else {
    echo "Error al guardar: " . $stmt->error;
}

$stmt->close();
$db->close();
?>
