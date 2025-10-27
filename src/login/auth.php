<?php
session_start();
require_once "config/Database.php";

$conn = Database::getInstance();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = $_POST["usuario"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT u.id, u.usuario, u.contrasena_hash, r.nombre AS rol 
                            FROM usuarios u
                            JOIN roles r ON u.rol_id = r.id
                            WHERE u.usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user["contrasena_hash"])) {
        $_SESSION["usuario_id"] = $user["id"];
        $_SESSION["usuario_nombre"] = $user["usuario"];
        $_SESSION["usuario_rol"] = $user["rol"];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Usuario o contraseña incorrectos";
    }
}
?>
