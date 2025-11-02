<?php
require_once "../db/conexion.php"; 
// Solo admins
require_once "../login/check_admin.php";


$mensaje = "";

// Crear usuario
if (isset($_POST['crear_usuario'])) {
    $rol_id = $_POST['rol_id'];
    $usuario = trim($_POST['usuario']);
    $password = $_POST['password'];
    $nombre_completo = trim($_POST['nombre_completo']);
    $correo = trim($_POST['correo']);
    $telefono = trim($_POST['telefono']);

    if ($rol_id && $usuario && $password) {
        $con = conectar();

        $stmt = $con->prepare("SELECT id FROM usuarios WHERE usuario = ?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $mensaje = "El usuario ya existe.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt2 = $con->prepare("INSERT INTO usuarios (rol_id, usuario, contrasena_hash, nombre_completo, correo, telefono, creado_en) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $stmt2->bind_param("isssss", $rol_id, $usuario, $hash, $nombre_completo, $correo, $telefono);
            if ($stmt2->execute()) {
                $mensaje = "Usuario creado correctamente.";
            } else {
                $mensaje = "Error al crear usuario: " . $stmt2->error;
            }
            $stmt2->close();
        }

        $stmt->close();
        desconectar($con);
    } else {
        $mensaje = "Todos los campos obligatorios deben estar llenos.";
    }
}

// Eliminar usuario
if (isset($_GET['eliminar'])) {
    $id = (int)$_GET['eliminar'];
    $con = conectar();
    $stmt = $con->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $mensaje = "Usuario eliminado.";
    }
    $stmt->close();
    desconectar($con);
}

// Actualizar rol
if (isset($_POST['actualizar_rol'])) {
    $id = $_POST['usuario_id'];
    $nuevo_rol = $_POST['nuevo_rol'];
    $con = conectar();
    $stmt = $con->prepare("UPDATE usuarios SET rol_id = ? WHERE id = ?");
    $stmt->bind_param("ii", $nuevo_rol, $id);
    if ($stmt->execute()) {
        $mensaje = "Rol actualizado.";
    }
    $stmt->close();
    desconectar($con);
}

// Obtener lista de usuarios
$con = conectar();
$usuarios_res = $con->query("SELECT u.id, u.usuario, u.nombre_completo, u.correo, u.telefono, r.nombre as rol_nombre, r.id as rol_id FROM usuarios u INNER JOIN roles r ON u.rol_id = r.id ORDER BY u.id ASC");
$usuarios = $usuarios_res->fetch_all(MYSQLI_ASSOC);

// Obtener roles para select
$roles_res = $con->query("SELECT id, nombre FROM roles ORDER BY id ASC");
$roles = $roles_res->fetch_all(MYSQLI_ASSOC);
desconectar($con);

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Administrar Usuarios</title>
<style>
    body { font-family: Arial; margin: 20px; }
    table { border-collapse: collapse; width: 100%; margin-top: 20px; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
    th { background-color: #0077b6; color: white; }
    input, select, button { padding: 5px; margin: 2px; }
    button { cursor: pointer; }
    .mensaje { color: red; font-weight: bold; text-align: center; margin: 10px 0; }
</style>
</head>
<body>
<h2>Panel de Administración - Usuarios</h2>
<div class="mensaje"><?= $mensaje ?></div>

<h3>Crear Usuario</h3>
<form method="POST">
    <input type="text" name="usuario" placeholder="Usuario" required>
    <input type="password" name="password" placeholder="Contraseña" required>
    <input type="text" name="nombre_completo" placeholder="Nombre completo">
    <input type="email" name="correo" placeholder="Correo">
    <input type="tel" name="telefono" placeholder="Teléfono">
    <select name="rol_id" required>
        <option value="">Selecciona rol</option>
        <?php foreach($roles as $rol): ?>
            <option value="<?= $rol['id'] ?>"><?= htmlspecialchars($rol['nombre']) ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit" name="crear_usuario">Crear</button>
</form>

<h3>Usuarios Registrados</h3>
<table>
    <tr>
        <th>ID</th>
        <th>Usuario</th>
        <th>Nombre completo</th>
        <th>Correo</th>
        <th>Teléfono</th>
        <th>Rol</th>
        <th>Acciones</th>
    </tr>
    <?php foreach($usuarios as $u): ?>
    <tr>
        <td><?= $u['id'] ?></td>
        <td><?= htmlspecialchars($u['usuario']) ?></td>
        <td><?= htmlspecialchars($u['nombre_completo']) ?></td>
        <td><?= htmlspecialchars($u['correo']) ?></td>
        <td><?= htmlspecialchars($u['telefono']) ?></td>
        <td>
            <form method="POST" style="display:inline;">
                <input type="hidden" name="usuario_id" value="<?= $u['id'] ?>">
                <select name="nuevo_rol" onchange="this.form.submit()">
                    <?php foreach($roles as $rol): ?>
                        <option value="<?= $rol['id'] ?>" <?= $rol['id']==$u['rol_id']?'selected':'' ?>><?= htmlspecialchars($rol['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" name="actualizar_rol">
            </form>
        </td>
        <td>
            <a href="?eliminar=<?= $u['id'] ?>" onclick="return confirm('¿Eliminar este usuario?');">Eliminar</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
</body>
</html>