<?php
session_start();
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

// Ejemplo: restringir solo a administradores
function requireAdmin() {
    if ($_SESSION["usuario_rol"] !== "admin") {
        echo "<h2>Acceso denegado</h2>";
        exit;
    }
}
?>
