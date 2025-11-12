<?php
include("../db/conexion.php");
$conn = conectar();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: reservations.php?error=Método no permitido');
    exit();
}

$nombre = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefono = trim($_POST['phone'] ?? '');
$sucursal_id = intval($_POST['branch'] ?? 0);
$fecha = $_POST['date'] ?? '';
$hora = $_POST['time'] ?? '';
$personas = intval($_POST['guests'] ?? 1);
$comentarios = trim($_POST['comments'] ?? '');

$errores = [];
if (empty($nombre) || strlen($nombre) < 2) $errores[] = 'Nombre inválido';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errores[] = 'Email inválido';
if (empty($telefono) || strlen($telefono) < 10) $errores[] = 'Teléfono inválido';
if ($sucursal_id <= 0) $errores[] = 'Sucursal inválida';
if (empty($fecha) || strtotime($fecha) < strtotime(date('Y-m-d'))) $errores[] = 'Fecha inválida (debe ser hoy o futura)';
if (empty($hora)) $errores[] = 'Hora inválida';
if ($personas < 1 || $personas > 100) $errores[] = 'Número de personas inválido';

if (empty($errores)) {
    $check_sql = "SELECT id FROM sucursales WHERE id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $sucursal_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    if ($check_result->num_rows === 0) {
        $errores[] = 'Sucursal no existe en la base de datos';
    }
    $check_stmt->close();
}

if (!empty($errores)) {
    $mensaje_error = implode('; ', $errores);
    header('Location: reservations.php?error=' . urlencode($mensaje_error));
    exit();
}

try {
    $sql = "INSERT INTO reservaciones (nombre, email, telefono, sucursal_id, fecha, hora, personas, comentarios) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssissis", $nombre, $email, $telefono, $sucursal_id, $fecha, $hora, $personas, $comentarios);
    if ($stmt->execute()) {
        $mensaje = "¡Reservación guardada exitosamente! ID: " . $conn->insert_id . ". Te contactaremos pronto.";
    } else {
        throw new Exception("Error ejecutando consulta: " . $stmt->error);
    }
    $stmt->close();
} catch (Exception $e) {
    header('Location: reservations.php?error=Error al guardar: ' . urlencode($e->getMessage()));
    exit();
}

$conn->close();
header('Location: reservations.php?message=' . urlencode($mensaje));
exit();
?>