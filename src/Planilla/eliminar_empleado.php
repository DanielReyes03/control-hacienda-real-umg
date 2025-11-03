<?php
// eliminar_empleado.php - Elimina un empleado por ID y redirige a ver_empleados.php
// CORREGIDO: Primero elimina planillas relacionadas (manual cascade), luego el empleado

// Configuración de la base de datos
$host = 'db';
$user = 'user';
$password = 'userpassword';
$database = 'mydb';

// Crear conexión
$conn = new mysqli($host, $user, $password, $database);

// Revisar conexión
if ($conn->connect_error) {
    header("Location: ver_empleados.php?error=" . urlencode("Error de conexión: " . $conn->connect_error));
    exit();
}

// Obtener ID del empleado
$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: ver_empleados.php?error=" . urlencode("ID de empleado inválido."));
    exit();
}

// Verificar si existe el empleado
$check_sql = "SELECT id FROM empleados WHERE id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    $check_stmt->close();
    header("Location: ver_empleados.php?error=" . urlencode("Empleado no encontrado."));
    exit();
}

$check_stmt->close();

// Paso 1: Eliminar planillas relacionadas (manual cascade)
$delete_planilla_sql = "DELETE FROM planilla WHERE empleado_id = ?";
$delete_planilla_stmt = $conn->prepare($delete_planilla_sql);
$delete_planilla_stmt->bind_param("i", $id);

if (!$delete_planilla_stmt->execute()) {
    $delete_planilla_stmt->close();
    header("Location: ver_empleados.php?error=" . urlencode("Error al eliminar planillas relacionadas: " . $conn->error));
    exit();
}

$num_planillas = $delete_planilla_stmt->affected_rows;
$delete_planilla_stmt->close();

// Paso 2: Eliminar el empleado
$sql = "DELETE FROM empleados WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    // Redirigir con éxito (incluye info de planillas eliminadas si aplica)
    $msg = "Empleado eliminado exitosamente.";
    if ($num_planillas > 0) {
        $msg .= " Se eliminaron también $num_planillas planillas relacionadas.";
    }
    header("Location: ver_empleados.php?success=" . urlencode($msg));
} else {
    $stmt->close();
    $conn->close();
    // Redirigir con error
    header("Location: ver_empleados.php?error=" . urlencode("Error al eliminar el empleado: " . $conn->error));
}

exit();
?>