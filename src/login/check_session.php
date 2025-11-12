<?php
session_start();

// 1️⃣ Verificar si hay sesión activa
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol_id'])) {
    // Si no hay sesión → login
    header("Location: ../login/login.php");
    exit;
}

// 2️⃣ Redirigir según rol
switch ($_SESSION['rol_id']) {
    case 1: // Administrador
        header("Location: ../compras/VistaEmpleado/index.php");
        break;
    case 2: // Gerente
        header("Location: ../compras/VistaEmpleado/index.php");
        break;
    case 3: // Empleado
        header("Location: ../compras/VistaEmpleado/index.php");
        break;
    case 4: // Repartidor
        header("Location: ../compras/VistaEmpleado/index.php");
        break;
    case 5: // Cliente
        header("Location: ../compras/VistaCliente/index.php");
        break;
    default:
        // Rol desconocido → menú principal
        header("Location: ../index.php");
        break;
}

exit;
?>
