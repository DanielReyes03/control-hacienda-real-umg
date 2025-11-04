<?php
require_once "../login/check_adminGer.php";
include("../db/conexion.php");
$conn = conectar();

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: index.php?error=ID inválido');
    exit;
}

$stmt = $conn->prepare("DELETE FROM clientes WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        header('Location: index.php?success=Cliente eliminado exitosamente');
    } else {
        header('Location: index.php?error=Cliente no encontrado');
    }
} else {
    header('Location: index.php?error=Error al eliminar: ' . urlencode($conn->error));
}
$stmt->close();
$conn->close();
exit;
?>