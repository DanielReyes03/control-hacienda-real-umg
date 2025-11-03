<?php
require_once "../login/check_adminEmple.php";
// ver_empleados.php - Lista de empleados (versión corregida para deprecaciones de null en htmlspecialchars)
// Coloca este archivo en /var/www/html/planilla/ para ver empleados: http://tu-servidor/planilla/ver_empleados.php

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

// Manejo de mensajes de redirección
$mensaje_success = $_GET['success'] ?? '';
$mensaje_error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Módulo de Empleados - Hacienda Real</title>
  <link rel="stylesheet" href="styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../compartido/componentes/cabecera/cabecera.css">
  <!-- Importar SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <?php
    include("../compartido/componentes/cabecera/index.php");
    cabecera("Empleados");
  ?>

  <main class="contenido">
    <div class="acciones">
      <a href="procesar_empleado.php" class="btn-crear" onclick="alertaCrear(event)">Crear Nuevo Empleado</a>
      <a href="index.php" class="btn-regresar">Regresar a Planillas</a>
    </div>

    <div class="tabla-contenedor">
      <table>
        <tr>
          <th>ID</th>
          <th>Nombre Completo</th>
          <th>Cédula/DPI</th>
          <th>Puesto</th>
          <th>Salario</th>
          <th>Fecha Inicio</th>
          <th>Teléfono</th>
          <th>Correo</th>
          <th>Estado</th>
          <th>Notas</th>
          <th>Acciones</th>
        </tr>

        <?php
        // Consulta para mostrar empleados (usa 'nombre' concatenado, 'dpi' para cédula, etc.)
        // Muestra todos, pero puedes filtrar solo activos agregando WHERE activo = 1
        $sql = "SELECT id, nombres, dpi AS cedula, puesto, salario, fecha_inicio, telefono, correo, activo, notas 
                FROM empleados 
                ORDER BY id DESC";
        $resultado = $conn->query($sql);

        if ($resultado && $resultado->num_rows > 0) {
          while ($fila = $resultado->fetch_assoc()) {
            $id = $fila['id'];
            $estado = ($fila['activo'] ?? 0) ? 'Activo' : 'Inactivo';
            $puesto = $fila['puesto'] ?? 'No especificado';
            $salario = $fila['salario'] ?? 0;
            $fecha_inicio = $fila['fecha_inicio'] ?? '';
            $telefono = $fila['telefono'] ?? '';
            $correo = $fila['correo'] ?? '';
            $notas = $fila['notas'] ?? '';
            echo "<tr>
                    <td>" . htmlspecialchars($id) . "</td>
                    <td>" . htmlspecialchars($fila['nombre'] ?? '') . "</td>
                    <td>" . htmlspecialchars($fila['cedula'] ?? '') . "</td>
                    <td>" . htmlspecialchars($puesto) . "</td>
                    <td>" . htmlspecialchars(number_format($salario, 2)) . "</td>
                    <td>" . ($fecha_inicio ? date('d/m/Y', strtotime($fecha_inicio)) : '') . "</td>
                    <td>" . htmlspecialchars($telefono) . "</td>
                    <td>" . htmlspecialchars($correo) . "</td>
                    <td><span class='estado " . (($fila['activo'] ?? 0) ? 'success' : 'warning') . "'>" . $estado . "</span></td>
                    <td>" . htmlspecialchars(substr($notas, 0, 30) . (strlen($notas) > 30 ? '...' : '')) . "</td>
                    <td class='acciones'>
                      <a href='#' class='boton-editar' onclick='alertaEditar(event, $id)'>Editar</a>
                      <a href='#' class='boton-eliminar' onclick='alertaEliminar(event, $id)'>Eliminar</a>
                    </td>
                  </tr>";
          }
        } else {
          echo "<tr><td colspan=\"11\">No hay empleados registrados</td></tr>";
        }
        $conn->close();
        ?>
      </table>
    </div>
  </main>

  <?php if ($mensaje_success): ?>
  <script>
    Swal.fire({
      title: 'Éxito',
      text: '<?php echo htmlspecialchars($mensaje_success); ?>',
      icon: 'success',
      confirmButtonText: 'OK'
    });
  </script>
  <?php endif; ?>

  <?php if ($mensaje_error): ?>
  <script>
    Swal.fire({
      title: 'Error',
      text: '<?php echo htmlspecialchars($mensaje_error); ?>',
      icon: 'error',
      confirmButtonText: 'OK'
    });
  </script>
  <?php endif; ?>

  <script>
    // Alerta al crear nuevo empleado
    function alertaCrear(event) {
      event.preventDefault();
      Swal.fire({
        title: "¿Deseas crear un nuevo empleado?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Sí, crear",
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33"
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = "procesar_empleado.php";
        }
      });
    }

    // Alerta al editar
    function alertaEditar(event, id) {
      event.preventDefault();
      Swal.fire({
        title: "Editar empleado",
        text: "¿Deseas modificar la información de este empleado?",
        icon: "info",
        showCancelButton: true,
        confirmButtonText: "Sí, editar",
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33"
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = "editar_empleado.php?id=" + id;
        }
      });
    }

    // Alerta al eliminar
    function alertaEliminar(event, id) {
      event.preventDefault();
      Swal.fire({
        title: "¿Estás seguro?",
        text: "Esta acción eliminará el empleado permanentemente. ¿Continuar?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6"
      }).then((result) => {
        if (result.isConfirmed) {
          Swal.fire({
            title: "Eliminado",
            text: "El empleado ha sido eliminado correctamente.",
            icon: "success",
            timer: 1500,
            showConfirmButton: false
          });
          setTimeout(() => {
            window.location.href = "eliminar_empleado.php?id=" + id;
          }, 1500);
        }
      });
    }
  </script>
</body>
</html>