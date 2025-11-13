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

    <!-- Sección de Reservaciones -->
    <section class="reservations">
        <div class="container">
            <h2>Reservaciones</h2>
            <button id="toggleReservations" class="btn">Ver Reservaciones</button>
            <div id="reservationsTable" style="display: none;">
                <?php
                // Reconectar a la base de datos para las reservaciones
                $conn = conectar();
                $sql_reservaciones = "SELECT r.*, s.nombre AS sucursal_nombre FROM reservaciones r JOIN sucursales s ON r.sucursal_id = s.id ORDER BY r.fecha_creacion DESC";
                $resultado_reservaciones = $conn->query($sql_reservaciones);

                if ($resultado_reservaciones && $resultado_reservaciones->num_rows > 0) {
                    echo "<table class='table'>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                    <th>Sucursal</th>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Personas</th>
                                    <th>Comentarios</th>
                                    <th>Fecha Creación</th>
                                </tr>
                            </thead>
                            <tbody>";
                    while ($fila_reservacion = $resultado_reservaciones->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($fila_reservacion['id']) . "</td>
                                <td>" . htmlspecialchars($fila_reservacion['nombre']) . "</td>
                                <td>" . htmlspecialchars($fila_reservacion['email']) . "</td>
                                <td>" . htmlspecialchars($fila_reservacion['telefono']) . "</td>
                                <td>" . htmlspecialchars($fila_reservacion['sucursal_nombre']) . "</td>
                                <td>" . htmlspecialchars($fila_reservacion['fecha']) . "</td>
                                <td>" . htmlspecialchars($fila_reservacion['hora']) . "</td>
                                <td>" . htmlspecialchars($fila_reservacion['personas']) . "</td>
                                <td>" . htmlspecialchars($fila_reservacion['comentarios'] ?? '') . "</td>
                                <td>" . htmlspecialchars($fila_reservacion['fecha_creacion']) . "</td>
                              </tr>";
                    }
                    echo "</tbody></table>";
                } else {
                    echo "<p>No hay reservaciones disponibles en este momento.</p>";
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

    <script>
        // Función para alternar la visibilidad de la tabla de reservaciones
        document.getElementById('toggleReservations').addEventListener('click', function() {
            var table = document.getElementById('reservationsTable');
            if (table.style.display === 'none') {
                table.style.display = 'block';
                this.textContent = 'Ocultar Reservaciones';
            } else {
                table.style.display = 'none';
                this.textContent = 'Ver Reservaciones';
            }
        });
    </script>
</body>
</html>
