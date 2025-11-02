<?php
error_reporting(E_ALL);
include_once("../../../db/conexion.php");

// Validar parámetros requeridos
if(!isset($_GET["nombre"], $_GET["detalle"])) {
    echo "Faltan parámetros";
    exit;
}

$nombre = trim($_GET["nombre"]);
$descripcion = isset($_GET["descripcion"]) ? trim($_GET["descripcion"]) : "";
$detalleJson = $_GET["detalle"];

// Validaciones básicas
if (!preg_match("/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\-\(\)\.]+$/", $nombre)) {
    echo "Nombre inválido";
    exit;
}

$detalle = json_decode($detalleJson, true);
if (!is_array($detalle) || count($detalle) == 0) {
    echo "Detalle de receta inválido";
    exit;
}

// 🔹 Validar que no existan materias primas repetidas
$idsMaterias = array_column($detalle, "materia_prima_id");
$duplicados = array_diff_assoc($idsMaterias, array_unique($idsMaterias));

if (count($duplicados) > 0) {
    echo "La receta contiene materias primas repetidas";
    exit;
}

// Conexión a la base de datos
$db = conectar();
$db->begin_transaction();

try {
    // Verificar si ya existe una receta con el mismo nombre
    $checkSql = "SELECT id FROM recetas WHERE nombre = ?";
    $checkStmt = $db->prepare($checkSql);
    $checkStmt->bind_param("s", $nombre);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        echo "Ya existe una receta con ese nombre";
        $checkStmt->close();
        $db->close();
        exit;
    }
    $checkStmt->close();

    // Insertar encabezado
    $sqlReceta = "INSERT INTO recetas (nombre, descripcion) VALUES (?, ?)";
    $stmtReceta = $db->prepare($sqlReceta);
    $stmtReceta->bind_param("ss", $nombre, $descripcion);
    $stmtReceta->execute();
    $receta_id = $stmtReceta->insert_id;
    $stmtReceta->close();

    // Insertar detalle (ya validado sin duplicados)
    $sqlDet = "INSERT INTO receta_detalle (receta_id, materia_prima_id, cantidad) VALUES (?, ?, ?)";
    $stmtDet = $db->prepare($sqlDet);

    foreach($detalle as $item) {
        $materia = intval($item["materia_prima_id"]);
        $cantidad = floatval($item["cantidad"]);

        if ($materia <= 0 || $cantidad <= 0) continue;

        $stmtDet->bind_param("iid", $receta_id, $materia, $cantidad);
        $stmtDet->execute();
    }
    $stmtDet->close();

    $db->commit();
    echo "Ok";

} catch (Exception $e) {
    $db->rollback();
    echo "Error al guardar: " . $e->getMessage();
}

$db->close();
?>
