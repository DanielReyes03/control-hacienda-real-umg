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

// Obtener datos del formulario
$nombre = $_POST['nombre'];
$telefono = $_POST['telefono'];
$correo = $_POST['correo'];
$direccion = $_POST['direccion'];

// Insertar datos en la tabla clientes
$sql = "INSERT INTO clientes (nombre, telefono, correo, direccion)
        VALUES ('$nombre', '$telefono', '$correo', '$direccion')";

if ($conn->query($sql) === TRUE) {
    header("Location: index.php"); // Redirige a la lista de clientes
    exit;
} else {
    echo "Error al registrar cliente: " . $conn->error;
}

$conn->close();
?>
