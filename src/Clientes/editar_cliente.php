<?php
// Conectar a la base de datos
$host = 'db';
$user = 'user';
$password = 'userpassword';
$database = 'mydb';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Si se recibió el formulario para actualizar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];
    $direccion = $_POST['direccion'];

    $sql = "UPDATE clientes 
            SET nombre='$nombre', telefono='$telefono', correo='$correo', direccion='$direccion'
            WHERE id_cliente=$id";

    if ($conn->query($sql) === TRUE) {
        header("Location: index.php");
        exit;
    } else {
        echo "Error al actualizar cliente: " . $conn->error;
    }
}

// Si se pasa un ID por la URL, obtener los datos del cliente
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $resultado = $conn->query("SELECT * FROM clientes WHERE id_cliente = $id");

    if ($resultado->num_rows == 1) {
        $cliente = $resultado->fetch_assoc();
    } else {
        echo "Cliente no encontrado.";
        exit;
    }
} else {
    echo "ID de cliente no proporcionado.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Cliente - Hacienda Real</title>
  <link rel="stylesheet" href="clientes.css">
</head>
<body>
  <div class="contenedor">
    <h1>Editar Cliente</h1>
    <form class="formulario" action="" method="POST">
      <input type="hidden" name="id" value="<?php echo $cliente['id_cliente']; ?>">
      <input type="text" name="nombre" value="<?php echo $cliente['nombre']; ?>" required>
      <input type="text" name="telefono" value="<?php echo $cliente['telefono']; ?>">
      <input type="email" name="correo" value="<?php echo $cliente['correo']; ?>">
      <input type="text" name="direccion" value="<?php echo $cliente['direccion']; ?>">
      <button type="submit">Guardar Cambios</button>
    </form>
  </div>
</body>
</html>
