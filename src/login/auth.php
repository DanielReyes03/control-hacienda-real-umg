<?php
session_start();
require_once __DIR__ . '/../db/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit();
}

$usuario = trim($_POST['usuario'] ?? '');
$password = $_POST['password'] ?? '';

if ($usuario === '' || $password === '') {
    $_SESSION['error'] = "Completa usuario y contraseña.";
    header("Location: login.php");
    exit();
}

$conn = conectar();

// Login usando el campo "usuario"
$stmt = $conn->prepare("SELECT id, rol_id, contrasena_hash, nombre_completo FROM usuarios WHERE usuario = ?");
$stmt->bind_param("s", $usuario);
$stmt->execute();
$res = $stmt->get_result();

if ($res && $res->num_rows === 1) {
    $row = $res->fetch_assoc();
    if (password_verify($password, $row['contrasena_hash'])) {
        // Login correcto
        $_SESSION['usuario_id'] = $row['id'];
        $_SESSION['rol_id'] = $row['rol_id'];
        $_SESSION['nombre_completo'] = $row['nombre_completo'];

        $stmt->close();
        desconectar($conn);

        header("Location: ../inicio/index.php");
        exit();
    } else {
        $_SESSION['error'] = "Contraseña incorrecta.";
    }
} else {
    $_SESSION['error'] = "Usuario no encontrado.";
}

$stmt->close();
desconectar($conn);
header("Location: login.php");
exit();
?>
