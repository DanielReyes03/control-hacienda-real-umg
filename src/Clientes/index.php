<?php
// Configuración de la base de datos
$host = 'db';        // nombre del servicio MySQL en docker-compose
$user = 'user';
$password = 'userpassword';
$database = 'mydb';

// Crear conexión
$conn = new mysqli($host, $user, $password, $database);

// Revisar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}


?>

<<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Nombre del módulo</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href=".css">
</head>
<body>
  <header class="encabezado">
    <button class="btn-volver">←</button>
    <h1>Nombre del módulo</h1>
  </header>
</body>
</html>
