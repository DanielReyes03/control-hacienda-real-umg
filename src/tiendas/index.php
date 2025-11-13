<?php
require_once "../login/check_adminEmple.php"; 
include("../db/conexion.php");
$conn = conectar();

$mensaje_success = isset($_GET['success']) ? $_GET['success'] : '';
$mensaje_error = isset($_GET['error']) ? $_GET['error'] : '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Módulo de Sucursales - Hacienda Real</title>
  <link rel="stylesheet" href="styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../compartido/componentes/cabecera/cabecera.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <?php
    include("../compartido/componentes/cabecera/index.php");
    cabecera("Sucursales");
  ?>

  <main class="contenido">
    <div class="acciones">
      <a href="#" class="btn-crear" onclick="alertaCrear(event)">Crear Nueva</a>
    </div>

    <div class="tabla-contenedor">
      <table>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Dirección</th>
          <th>Gerente ID</th>
          <th>Teléfono</th>
          <th>Número de Mesas</th>
          <th>Horarios</th>
          <th>Características</th>
          <th>Calificación</th>
          <th>Capacidad</th>
          <th>Creada el</th>
          <th>Acciones</th>
        </tr>

        <?php
        $sql = "SELECT * FROM sucursales ORDER BY id DESC";
        $resultado = $conn->query($sql);

        if ($resultado && $resultado->num_rows > 0) {
          while ($fila = $resultado->fetch_assoc()) {
            // Evitar valores NULL
            $id = !empty($fila['id']) ? $fila['id'] : '';
            $nombre = !empty($fila['nombre']) ? $fila['nombre'] : '';
            $direccion = !empty($fila['direccion']) ? $fila['direccion'] : '';
            $gerente_id = !empty($fila['gerente_id']) ? $fila['gerente_id'] : '';
            $telefono = !empty($fila['telefono']) ? $fila['telefono'] : '';
            $numero_mesas = !empty($fila['numero_mesas']) ? $fila['numero_mesas'] : '';
            $horarios = !empty($fila['horarios']) ? $fila['horarios'] : '';
            $caracteristicas_full = !empty($fila['caracteristicas']) ? $fila['caracteristicas'] : '';
            $calificacion = !empty($fila['calificacion']) ? $fila['calificacion'] : '';
            $capacidad = !empty($fila['capacidad']) ? $fila['capacidad'] : '';
            $creado_en = !empty($fila['creado_en']) ? date('d/m/Y', strtotime($fila['creado_en'])) : '';

            // Truncar características si es larga (opcional, para tabla limpia)
            $caracteristicas = strlen($caracteristicas_full) > 30 ? substr($caracteristicas_full, 0, 30) . '...' : $caracteristicas_full;

            echo "<tr>
                    <td>" . htmlspecialchars($id) . "</td>
                    <td>" . htmlspecialchars($nombre) . "</td>
                    <td>" . htmlspecialchars($direccion) . "</td>
                    <td>" . htmlspecialchars($gerente_id) . "</td>
                    <td>" . htmlspecialchars($telefono) . "</td>
                    <td>" . htmlspecialchars($numero_mesas) . "</td>
                    <td title='" . htmlspecialchars($horarios) . "'>" . htmlspecialchars($horarios) . "</td>
                    <td title='" . htmlspecialchars($caracteristicas_full) . "'>" . htmlspecialchars($caracteristicas) . "</td>
                    <td>" . htmlspecialchars($calificacion) . "</td>
                    <td>" . htmlspecialchars($capacidad) . "</td>
                    <td>" . htmlspecialchars($creado_en) . "</td>
                    <td class='acciones'>
                      <a href='#' class='boton-editar' onclick='alertaEditar(event, {$id})'>Editar</a>
                      <a href='#' class='boton-eliminar' onclick='alertaEliminar(event, {$id})'>Eliminar</a>
                    </td>
                  </tr>";
          }
        } else {
          echo "<tr><td colspan='12'>No hay sucursales registradas</td></tr>";
        }
        $conn->close();
        ?>
      </table>
    </div>
  </main>

  <?php if (!empty($mensaje_success)): ?>
  <script>
    Swal.fire('Éxito', '<?php echo htmlspecialchars($mensaje_success); ?>', 'success');
  </script>
  <?php endif; ?>

  <?php if (!empty($mensaje_error)): ?>
  <script>
    Swal.fire('Error', '<?php echo htmlspecialchars($mensaje_error); ?>', 'error');
  </script>
  <?php endif; ?>

  <script>
    function alertaCrear(event) {
      event.preventDefault();
      Swal.fire({
        title: "¿Deseas crear una nueva sucursal?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Sí, crear",
        cancelButtonText: "Cancelar"
      }).then((r) => {
        if (r.isConfirmed) window.location.href = "procesar_tienda.php";
      });
    }

    function alertaEditar(event, id) {
      event.preventDefault();
      Swal.fire({
        title: "Editar sucursal",
        text: "¿Deseas modificar la información de esta sucursal?",
        icon: "info",
        showCancelButton: true,
        confirmButtonText: "Sí, editar",
        cancelButtonText: "Cancelar"
      }).then((r) => {
        if (r.isConfirmed) window.location.href = "editar_tienda.php?id=" + id;
      });
    }

    function alertaEliminar(event, id) {
      event.preventDefault();
      Swal.fire({
        title: "¿Eliminar sucursal?",
        text: "Esta acción no se puede deshacer.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar"
      }).then((r) => {
        if (r.isConfirmed) {
          Swal.fire({title:"Eliminando...", icon:"success", timer:1200, showConfirmButton:false});
          setTimeout(()=>window.location.href="eliminar_tienda.php?id="+id,1200);
        }
      });
    }
  </script>
</body>
</html>