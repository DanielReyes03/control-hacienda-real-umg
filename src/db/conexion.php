<?php

function conectar() {
    $conexion = new mysqli('db', 'user', 'userpassword', 'mydb', 3306);
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }
    return $conexion;
}

function desconectar($conn) {
    $conn->close();
    echo "\n\nConexión cerrada.";
}
?>