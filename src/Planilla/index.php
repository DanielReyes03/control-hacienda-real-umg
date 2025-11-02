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

// Manejo de mensajes de redirección
$mensaje_success = $_GET['success'] ?? '';
$mensaje_error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Módulo de Planillas - Hacienda Real</title>
  <link rel="stylesheet" href="styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../compartido/componentes/cabecera/cabecera.css">
  <!-- Importar SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <?php
    include("../compartido/componentes/cabecera/index.php");
    cabecera("Planillas");
  ?>

  <main class="contenido">
    <div class="acciones">
      <a href="procesar_planilla.php" class="btn-crear" onclick="alertaCrear(event)">Crear Nueva</a>
      <a href="procesar_empleado.php" class="btn-crear" onclick="alertaCrearEmpleado(event)">Crear Nuevo Empleado</a>
    </div>

    <div class="tabla-contenedor">
      <table>
        <tr>
          <th>ID</th>
          <th>Empleado ID</th>
          <th>Puesto</th>
          <th>Período Inicio</th>
          <th>Período Fin</th>
          <th>Sueldo Bruto</th>
          <th>Deducciones</th>
          <th>Sueldo Neto</th>
          <th>Fecha Pago</th>
          <th>Notas</th>
          <th>Acciones</th>
        </tr>

        <?php
        $sql = "SELECT * FROM planilla ORDER BY id DESC";
        $resultado = $conn->query($sql);

        if ($resultado && $resultado->num_rows > 0) {
          while ($fila = $resultado->fetch_assoc()) {
            $id = $fila['id']; // No htmlspecialchars para JS numérico
            echo "<tr>
                    <td>" . htmlspecialchars($id) . "</td>
                    <td>" . htmlspecialchars($fila['empleado_id']) . "</td>
                    <td>" . htmlspecialchars($fila['puesto']) . "</td>
                    <td>" . ($fila['periodo_inicio'] ? date('d/m/Y', strtotime($fila['periodo_inicio'])) : '') . "</td>
                    <td>" . ($fila['periodo_fin'] ? date('d/m/Y', strtotime($fila['periodo_fin'])) : '') . "</td>
                    <td>" . htmlspecialchars(number_format($fila['sueldo_bruto'], 2)) . "</td>
                    <td>" . htmlspecialchars(number_format($fila['deducciones'], 2)) . "</td>
                    <td>" . htmlspecialchars(number_format($fila['sueldo_neto'], 2)) . "</td>
                    <td>" . ($fila['fecha_pago'] ? date('d/m/Y', strtotime($fila['fecha_pago'])) : '') . "</td>
                    <td>" . htmlspecialchars(substr($fila['notas'] ?? '', 0, 50) . (strlen($fila['notas'] ?? '') > 50 ? '...' : '')) . "</td>
                    <td class='acciones'>
                      <a href='#' class='boton-editar' onclick='alertaEditar(event, $id)'>Editar</a>
                      <a href='#' class='boton-eliminar' onclick='alertaEliminar(event, $id)'>Eliminar</a>
                    </td>
                  </tr>";
          }
        } else {
          echo "<tr><td colspan=\"11\">No hay planillas registradas</td></tr>";
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
    // Alerta al crear nueva planilla
    function alertaCrear(event) {
      event.preventDefault();
      Swal.fire({
        title: "¿Deseas crear una nueva planilla?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Sí, crear",
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33"
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = "procesar_planilla.php";
        }
      });
    }

    // Alerta al crear nuevo empleado
    function alertaCrearEmpleado(event) {
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
      console.log("ID para editar:", id); // Debug: verifica en consola F12
      Swal.fire({
        title: "Editar planilla",
        text: "¿Deseas modificar la información de esta planilla?",
        icon: "info",
        showCancelButton: true,
        confirmButtonText: "Sí, editar",
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33"
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = "editar_planilla.php?id=" + id;
        }
      });
    }

    // Alerta al eliminar
    function alertaEliminar(event, id) {
      event.preventDefault();
      Swal.fire({
        title: "¿Estás seguro?",
        text: "Esta acción eliminará la planilla permanentemente.",
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
            text: "La planilla ha sido eliminada correctamente.",
            icon: "success",
            timer: 1500,
            showConfirmButton: false
          });
          setTimeout(() => {
            window.location.href = "eliminar_planilla.php?id=" + id;
          }, 1500);
        }
      });
    }
  </script>
</body>
</html>