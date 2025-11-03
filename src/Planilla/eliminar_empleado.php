<?php
$host = 'db';
$user = 'user';
$password = 'userpassword';
$database = 'mydb';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    header("Location: ver_empleados.php?error=" . urlencode("Error de conexión: " . $conn->connect_error));
    exit();
}

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: ver_empleados.php?error=" . urlencode("ID de empleado inválido."));
    exit();
}

$check_sql = "SELECT id FROM empleados WHERE id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $id);
$check_stmt->execute();
$result = $check_stmt->get_result();
if ($result->num_rows === 0) {
    $check_stmt->close();
    header("Location: ver_empleados.php?error=" . urlencode("Empleado no encontrado."));
    exit();
}
$check_stmt->close();

$delete_planilla_sql = "DELETE FROM planilla WHERE empleado_id = ?";
$delete_planilla_stmt = $conn->prepare($delete_planilla_sql);
$delete_planilla_stmt->bind_param("i", $id);
$delete_planilla_stmt->execute();
$num_planillas = $delete_planilla_stmt->affected_rows;
$delete_planilla_stmt->close();

$update_sucursal_sql = "UPDATE sucursales SET gerente_id = NULL WHERE gerente_id = ?";
$update_sucursal_stmt = $conn->prepare($update_sucursal_sql);
$update_sucursal_stmt->bind_param("i", $id);
$update_sucursal_stmt->execute();
$num_sucursales = $update_sucursal_stmt->affected_rows;
$update_sucursal_stmt->close();

// Paso 3: Eliminar el empleado
$sql = "DELETE FROM empleados WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $msg = "Empleado eliminado exitosamente.";
    if ($num_planillas > 0) {
        $msg .= " Se eliminaron $num_planillas planillas relacionadas.";
    }
    if ($num_sucursales > 0) {
        $msg .= " También se desasociaron $num_sucursales sucursales que tenía asignadas como gerente.";
    }

    $stmt->close();
    $conn->close();
    header("Location: ver_empleados.php?success=" . urlencode($msg));
    exit();
} else {
    $error_msg = "Error al eliminar el empleado: " . $stmt->error;
    $stmt->close();
    $conn->close();
    header("Location: ver_empleados.php?error=" . urlencode($error_msg));
    exit();
}
?>
