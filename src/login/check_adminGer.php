<?php
session_start();

// Si no hay sesión activa → redirige al login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login/login.php");
    exit;
}

/**
 * Roles permitidos para esta página
 * Puedes pasar los IDs de los roles permitidos
 * Ejemplo: 1 => Administrador, 2 => Gerente, 3 => Empleado, 4 => Repartidor
 */
$roles_permitidos = [1,2]; // Por defecto solo admin, cambia según necesidad

// Si quieres que la página defina sus roles permitidos, puedes hacer:
// $roles_permitidos = [1, 2]; // admin + gerente

// Verifica si el rol del usuario está en los permitidos
if (!in_array($_SESSION['rol_id'], $roles_permitidos)) {
    // Usuario no tiene permiso → SweetAlert
    echo '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Acceso denegado</title>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
    <script>
        Swal.fire({
            icon: "error",
            title: "Acceso denegado",
            text: "¡No tienes permiso para entrar a esta sección!",
            confirmButtonText: "Aceptar"
        }).then((result) => {
            if(result.isConfirmed){
                window.location.href = "../inicio/index.php"; // Redirige al menú principal
            }
        });
    </script>
    </body>
    </html>
    ';
    exit;
}
