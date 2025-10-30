<?php
error_reporting(E_ALL);
include_once("../../db/conexion.php");

// Validar parámetro requerido
if (!isset($_GET["id"])) {
    echo "Falta el parámetro ID";
    exit;
}

$id = intval($_GET["id"]);

if ($id <= 0) {
    echo "ID inválido";
    exit;
}

// Conexión a la base de datos
$db = conectar();

// Verificar si existe la materia prima
$checkSql = "SELECT id FROM inventario_materias_primas WHERE id = ?";
$checkStmt = $db->prepare($checkSql);
$checkStmt->bind_param("i", $id);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows === 0) {
    echo "La materia prima no existe";
    $checkStmt->close();
    $db->close();
    exit;
}
$checkStmt->close();

// Eliminar el registro
$sql = "DELETE FROM inventario_materias_primas WHERE id = ?";
$stmt = $db->prepare($sql);

if ($stmt === false) {
    echo "Error en la preparación: " . $db->error;
    exit;
}

$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "Ok";
} else {
    echo "Error al eliminar: " . $stmt->error;
}

$stmt->close();
$db->close();
?>
