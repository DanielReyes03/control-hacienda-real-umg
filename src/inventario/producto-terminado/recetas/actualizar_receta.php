<?php
error_reporting(E_ALL);
include_once("../../../db/conexion.php");

if(!isset($_GET["id"], $_GET["nombre"], $_GET["detalle"])) {
    echo "Faltan parámetros";
    exit;
}

$id = intval($_GET["id"]);
$nombre = trim($_GET["nombre"]);
$descripcion = isset($_GET["descripcion"]) ? trim($_GET["descripcion"]) : "";
$detalleJson = $_GET["detalle"];

if ($id <= 0) {
    echo "ID inválido";
    exit;
}

if (!preg_match("/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\-\(\)\.]+$/", $nombre)) {
    echo "Nombre inválido";
    exit;
}

$detalle = json_decode($detalleJson, true);
if (!is_array($detalle)) {
    echo "Detalle inválido";
    exit;
}

$db = conectar();
$db->begin_transaction();

try {
    // Verificar nombre duplicado
    $check = $db->prepare("SELECT id FROM recetas WHERE nombre = ? AND id <> ?");
    $check->bind_param("si", $nombre, $id);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        echo "Ya existe una receta con ese nombre";
        $check->close();
        $db->close();
        exit;
    }
    $check->close();

    // Actualizar encabezado
    $stmt = $db->prepare("UPDATE recetas SET nombre = ?, descripcion = ? WHERE id = ?");
    $stmt->bind_param("ssi", $nombre, $descripcion, $id);
    $stmt->execute();
    $stmt->close();

    // Eliminar detalle previo
    $db->query("DELETE FROM receta_detalle WHERE receta_id = " . $id);

    // Insertar nuevo detalle
    $sqlDet = "INSERT INTO receta_detalle (receta_id, materia_prima_id, cantidad) VALUES (?, ?, ?)";
    $stmtDet = $db->prepare($sqlDet);
    foreach($detalle as $item) {
        $materia = intval($item["materia_prima_id"]);
        $cantidad = floatval($item["cantidad"]);
        if ($materia <= 0 || $cantidad <= 0) continue;
        $stmtDet->bind_param("iid", $id, $materia, $cantidad);
        $stmtDet->execute();
    }
    $stmtDet->close();

    $db->commit();
    echo "Ok";
} catch (Exception $e) {
    $db->rollback();
    echo "Error al actualizar: " . $e->getMessage();
}

$db->close();
?>
