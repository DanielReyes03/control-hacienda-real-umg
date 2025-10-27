<?php
include("../conexion/conexion.php");
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: index.php?error=ID inválido');
    exit;
}

$stmt = $conn->prepare("DELETE FROM proveedores WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        header('Location: index.php?success=Proveedor eliminado exitosamente');
    } else {
        header('Location: index.php?error=Proveedor no encontrado');
    }
} else {
    header('Location: index.php?error=Error al eliminar: ' . urlencode($conn->error));
}
$stmt->close();
$conn->close();
exit;
?>