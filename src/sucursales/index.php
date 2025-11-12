<?php
require_once "../login/check_adminclient.php"; 
include("../db/conexion.php");
$conn = conectar();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sucursales - Hacienda Real Guatemala</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="../compartido/componentes/cabecera/cabecera.css">
</head>
<body>
<?php 
        include("../compartido/componentes/cabecera/index.php");
        cabecera("Sucursales"); 
    ?>

    <section class="branches">
        <div class="container">
            <h2>Nuestras Sucursales</h2>
            <div class="branch-grid">
                <?php
                $sql = "SELECT * FROM sucursales ORDER BY id ASC";
                $resultado = $conn->query($sql);

                if ($resultado && $resultado->num_rows > 0) {
                  while ($fila = $resultado->fetch_assoc()) {
                    // Evitar valores NULL
                    $id = !empty($fila['id']) ? $fila['id'] : '';
                    $nombre = !empty($fila['nombre']) ? $fila['nombre'] : '';
                    $direccion = !empty($fila['direccion']) ? $fila['direccion'] : '';
                    $telefono = !empty($fila['telefono']) ? $fila['telefono'] : '';
                    $horarios = !empty($fila['horarios']) ? $fila['horarios'] : '';
                    $caracteristicas = !empty($fila['caracteristicas']) ? $fila['caracteristicas'] : '';
                    $calificacion = !empty($fila['calificacion']) ? $fila['calificacion'] : '';

                    echo "
                    <div class='branch-card'>
                        <h3>" . htmlspecialchars($nombre) . "</h3>
                        <p><strong>Dirección:</strong> " . htmlspecialchars($direccion) . "</p>
                        <p><strong>Teléfono:</strong> " . htmlspecialchars($telefono) . "</p>
                        <p><strong>Horarios:</strong> " . htmlspecialchars($horarios) . "</p>
                        <p><strong>Características:</strong> " . htmlspecialchars($caracteristicas) . "</p>
                        <p><strong>Calificación:</strong> " . htmlspecialchars($calificacion) . "/5</p>
                        <a href='reservations.php' class='btn'>Reservar</a>
                    </div>";
                  }
                } else {
                  echo "<p>No hay sucursales disponibles en este momento.</p>";
                }
                $conn->close();
                ?>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 Hacienda Real Guatemala. Todos los derechos reservados.</p>
            <p>Visita <a href="https://haciendareal.net/" target="_blank">haciendareal.net</a> para más información.</p>
        </div>
    </footer>
</body>
</html>