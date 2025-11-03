<?php
require_once "../db/conexion.php"; 
require_once "../login/check_admin.php";

$mensaje = "";
$alerta = "";

// CREAR USUARIO
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
            $alerta = "error|El usuario ya existe.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt2 = $con->prepare("INSERT INTO usuarios (rol_id, usuario, contrasena_hash, nombre_completo, correo, telefono, creado_en) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $stmt2->bind_param("isssss", $rol_id, $usuario, $hash, $nombre_completo, $correo, $telefono);
            if ($stmt2->execute()) {
                $alerta = "success|Usuario creado correctamente.";
            } else {
                $alerta = "error|Error al crear usuario: " . $stmt2->error;
            }
            $stmt2->close();
        }

        $stmt->close();
        desconectar($con);
    } else {
        $alerta = "error|Todos los campos obligatorios deben estar llenos.";
    }
}

// ELIMINAR USUARIO
if (isset($_GET['eliminar'])) {
    $id = (int)$_GET['eliminar'];
    $con = conectar();
    $stmt = $con->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $alerta = "success|Usuario eliminado correctamente.";
    } else {
        $alerta = "error|No se pudo eliminar el usuario.";
    }
    $stmt->close();
    desconectar($con);
}

// ACTUALIZAR ROL
if (isset($_POST['actualizar_rol'])) {
    $id = $_POST['usuario_id'];
    $nuevo_rol = $_POST['nuevo_rol'];
    $con = conectar();
    $stmt = $con->prepare("UPDATE usuarios SET rol_id = ? WHERE id = ?");
    $stmt->bind_param("ii", $nuevo_rol, $id);
    if ($stmt->execute()) {
        $alerta = "success|Rol actualizado correctamente.";
    } else {
        $alerta = "error|No se pudo actualizar el rol.";
    }
    $stmt->close();
    desconectar($con);
}

// Obtener usuarios y roles
$con = conectar();
$usuarios_res = $con->query("SELECT u.id, u.usuario, u.nombre_completo, u.correo, u.telefono, r.nombre as rol_nombre, r.id as rol_id FROM usuarios u INNER JOIN roles r ON u.rol_id = r.id ORDER BY u.id ASC");
$usuarios = $usuarios_res->fetch_all(MYSQLI_ASSOC);

$roles_res = $con->query("SELECT id, nombre FROM roles ORDER BY id ASC");
$roles = $roles_res->fetch_all(MYSQLI_ASSOC);
desconectar($con);

// Cabecera
include("../compartido/componentes/cabecera/index.php");
cabecera("Usuarios");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Administrar Usuarios</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../compartido/componentes/cabecera/cabecera.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
/* Ajustes de estilo para que la cabecera llene la página */
body {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    background-color: #f4f4f4;
}

main.contenido {
    padding: 20px;
}

form, .tabla-contenedor {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

input, select, button {
    padding: 8px;
    font-size: 14px;
    border-radius: 4px;
    border: 1px solid #ccc;
    margin: 5px 0;
}

button {
    cursor: pointer;
    background-color: #0077b6;
    color: white;
    border:none;
    transition: 0.3s;
}

button:hover { background-color: #005f86; }

a.boton-eliminar {
    color: white;
    background-color: #d62828;
    padding: 5px 10px;
    border-radius:5px;
    text-decoration:none;
    transition:0.3s;
}

a.boton-eliminar:hover { background-color: #a71d2a; }

table {
    border-collapse: collapse;
    width: 100%;
    margin-top: 10px;
}

th, td {
    border: 1px solid #ccc;
    padding: 10px;
    text-align: center;
}

th {
    background-color: #0077b6;
    color: white;
}

select { cursor:pointer; }
</style>
</head>
<body>

<main class="contenido">
<h2>Panel de Administración - Usuarios</h2>

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
<div class="tabla-contenedor">
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
            <a href="?eliminar=<?= $u['id'] ?>" class="boton-eliminar" onclick="return confirm('¿Eliminar este usuario?');">Eliminar</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
</div>
</main>

<?php if($alerta): 
    list($tipo, $texto) = explode("|", $alerta, 2);
?>
<script>
Swal.fire({
    icon: '<?= $tipo ?>',
    title: '<?= ($tipo=='success') ? 'Éxito' : 'Error' ?>',
    text: '<?= $texto ?>',
    confirmButtonText: 'OK'
});
</script>
<?php endif; ?>

</body>
</html>
