<?php
// ¡TODO AQUÍ ARRIBA! Antes de cualquier output (HTML, espacios, etc.)
require_once "../login/check_adminclient.php"; 
include("../db/conexion.php");
$conn = conectar();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservaciones - Hacienda Real Guatemala</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="../compartido/componentes/cabecera/cabecera.css">
</head>
<body>
<?php 
    include("../compartido/componentes/cabecera/index.php");
    cabecera("Formulario Sucursales"); 
?>

<section class="reservation-form">
    <div class="container">
        <h2>Formulario de Reservación</h2>
        <?php if (isset($_GET['message'])): ?>
            <div style="text-align: center; margin-bottom: 20px; padding: 10px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 5px;">
                <?php echo htmlspecialchars($_GET['message']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div style="text-align: center; margin-bottom: 20px; padding: 10px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px;">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>
        <div class="form-container">
            <form action="Guardar_reservation.php" method="post">
                <div class="form-group">
                    <label for="name">Nombre Completo</label>
                    <input type="text" id="name" name="name" required placeholder="Ej. Juan Pérez">
                </div>
                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" required placeholder="juan@ejemplo.com">
                </div>
                <div class="form-group">
                    <label for="phone">Teléfono</label>
                    <input type="tel" id="phone" name="phone" required placeholder="Ej. +502 1234 5678">
                </div>
                <div class="form-group">
                    <label for="branch">Sucursal</label>
                    <select id="branch" name="branch" required>
                        <option value="" disabled selected>Selecciona una sucursal</option>
                        <?php
                        // Consulta para obtener sucursales (asumiendo tabla 'sucursales' con campos 'id' y 'nombre')
                        $sql = "SELECT id, nombre FROM sucursales ORDER BY nombre ASC";
                        $result = $conn->query($sql);

                        if ($result && $result->num_rows > 0) {
                            // Mostrar opciones dinámicamente
                            while($row = $result->fetch_assoc()) {
                                echo "<option value=\"" . htmlspecialchars($row["id"]) . "\">" . htmlspecialchars($row["nombre"]) . "</option>";
                            }
                        } else {
                            echo "<option value=\"\" disabled>No hay sucursales disponibles</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="date">Fecha</label>
                    <input type="date" id="date" name="date" required min="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="form-group">
                    <label for="time">Hora</label>
                    <input type="time" id="time" name="time" required>
                </div>
                <div class="form-group">
                    <label for="guests">Número de Personas</label>
                    <input type="number" id="guests" name="guests" min="1" max="100" required value="1">
                </div>
                <div class="form-group">
                    <label for="comments">Comentarios Adicionales</label>
                    <textarea id="comments" name="comments" rows="3" placeholder="Ej. Mesa en terraza, evento especial"></textarea>
                </div>
                <button type="submit" class="btn">Enviar Reservación</button>
            </form>
        </div>
    </div>
</section>

<footer class="footer">
    <div class="container">
        <p>&copy; 2025 Hacienda Real Guatemala. Todos los derechos reservados.</p>
        <p>Contáctanos al +502 2380-8383 para más información.</p>
    </div>
</footer>
</body>
</html>