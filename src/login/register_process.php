<?php
session_start();
require_once __DIR__ . '/../db/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit();
}

$nombre    = trim($_POST['nombre'] ?? '');
$usuario   = trim($_POST['usuario_reg'] ?? '');
$correo    = trim($_POST['correo_reg'] ?? '');
$telefono  = trim($_POST['telefono_reg'] ?? '');
$password  = $_POST['password_reg'] ?? '';
$password2 = $_POST['password_confirm'] ?? '';

if (!$nombre || !$usuario || !$correo || !$password || !$password2) {
    $_SESSION['error'] = "Todos los campos son obligatorios.";
    header("Location: login.php");
    exit();
}

if ($password !== $password2) {
    $_SESSION['error'] = "Las contraseñas no coinciden.";
    header("Location: login.php");
    exit();
}

$conn = conectar();

// Verificar si el usuario ya existe
$stmt = $conn->prepare("SELECT id FROM usuarios WHERE usuario = ?");
$stmt->bind_param("s", $usuario);
$stmt->execute();
$res = $stmt->get_result();
if ($res && $res->num_rows > 0) {
    $_SESSION['error'] = "El usuario ya existe.";
    $stmt->close();
    desconectar($conn);
    header("Location: login.php");
    exit();
}
$stmt->close();

// Insertar nuevo usuario con rol "Empleado" por defecto (rol_id=3)
$password_hash = password_hash($password, PASSWORD_BCRYPT);
$stmt = $conn->prepare("INSERT INTO usuarios (rol_id, usuario, contrasena_hash, nombre_completo, correo, telefono, creado_en) VALUES (?, ?, ?, ?, ?, ?, NOW())");
$rol_id = 5; // Cliente
$stmt->bind_param("isssss", $rol_id, $usuario, $password_hash, $nombre, $correo, $telefono);

if ($stmt->execute()) {
    $_SESSION['success'] = "Registro exitoso. Ya puedes iniciar sesión.";
} else {
    $_SESSION['error'] = "Error al registrar usuario.";
}

$stmt->close();
desconectar($conn);
header("Location: login.php");
exit();
?>
