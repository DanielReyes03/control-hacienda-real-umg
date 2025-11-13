<?php
require_once "../login/check_admin.php"; 
include("../db/conexion.php"); 
$conn = conectar();

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) { 
    header('Location: index.php?error=ID inválido'); 
    exit; 
}

$stmt = $conn->prepare("DELETE FROM sucursales WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        header('Location: index.php?success=Sucursal eliminada exitosamente');
    } else {
        header('Location: index.php?error=Sucursal no encontrada');
    }
} else {
    header('Location: index.php?error=Error al eliminar: ' . $conn->error);
}

$stmt->close();
$conn->close();
exit;
?>