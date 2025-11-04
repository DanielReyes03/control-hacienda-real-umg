<?php

require_once "../login/check_admin.php";
include("../db/conexion.php");
$conn = conectar();

// Obtener ID de la planilla
$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: index.php?error=" . urlencode("ID de planilla inválido."));
    exit();
}

// Verificar si existe la planilla
$check_sql = "SELECT id FROM planilla WHERE id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    $check_stmt->close();
    header("Location: index.php?error=" . urlencode("Planilla no encontrada."));
    exit();
}

$check_stmt->close();

// Eliminar la planilla
$sql = "DELETE FROM planilla WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    // Redirigir con éxito
    header("Location: index.php?success=" . urlencode("Planilla eliminada exitosamente."));
} else {
    $stmt->close();
    $conn->close();
    // Redirigir con error
    header("Location: index.php?error=" . urlencode("Error al eliminar la planilla: " . $conn->error));
}

exit();
?>