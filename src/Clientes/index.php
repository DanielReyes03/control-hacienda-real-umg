<?php
// Configuración de la base de datos
$host = 'db';
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
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Módulo de Clientes - Hacienda Real</title>
  <link rel="stylesheet" href="styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
  <header class="encabezado">
    <button class="btn-volver">←</button>
    <h1>Clientes</h1>
  </header>

  <main class="contenido">
    <div class="acciones">
      <a href="procesar_cliente.php" class="btn-crear">Crear Nuevo</a>
    </div>

    <div class="tabla-contenedor">
      <table>
        <tr>
          <th>ID</th>
          <th>DPI</th>
          <th>Nombre</th>
          <th>Teléfono</th>
          <th>Correo</th>
          <th>Dirección</th>
          <th>Creado en</th>
          <th>Acciones</th>
        </tr>

        <?php
        $sql = "SELECT * FROM clientes ORDER BY id DESC";
        $resultado = $conn->query($sql);

        if ($resultado && $resultado->num_rows > 0) {
          while ($fila = $resultado->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($fila['id']) . "</td>
                    <td>" . htmlspecialchars($fila['dpi'] ?? '') . "</td>
                    <td>" . htmlspecialchars($fila['nombre']) . "</td>
                    <td>" . htmlspecialchars($fila['telefono'] ?? '') . "</td>
                    <td>" . htmlspecialchars($fila['correo'] ?? '') . "</td>
                    <td>" . htmlspecialchars($fila['direccion'] ?? '') . "</td>
                    <td>" . ($fila['creado_en'] ? date('d/m/Y H:i', strtotime($fila['creado_en'])) : '') . "</td>
                    <td class='acciones'>
                      <a href='editar_cliente.php?id=" . htmlspecialchars($fila['id']) . "' class='boton-editar'>Editar</a>
                      <a href='eliminar_cliente.php?id=" . htmlspecialchars($fila['id']) . "' class='boton-eliminar' onclick='return confirm(\"¿Estás seguro de eliminar este cliente?\");'>Eliminar</a>
                    </td>
                  </tr>";
          }
        } else {
          echo "<tr><td colspan=\"8\">No hay clientes registrados</td></tr>";
        }
        $conn->close();
        ?>
      </table>
    </div>
  </main>
</body>
</html>