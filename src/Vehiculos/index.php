<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Módulo de Vehículos - Hacienda Real</title>
  <link rel="stylesheet" href="styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../compartido/componentes/cabecera/cabecera.css">
  <!-- Importar SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <?php
    include("../compartido/componentes/cabecera/index.php");
    include("../db/conexion.php");
    cabecera("Vehículos");
    $conn = conectar();
  ?>

  <main class="contenido">
    <div class="acciones">
      <a href="#" class="btn-crear" onclick="alertaCrear(event)">Crear Nuevo</a>
    </div>

    <div class="tabla-contenedor">
      <table>
        <tr>
          <th>ID</th>
          <th>Sucursal</th>
          <th>Placa</th>
          <th>Modelo</th>
          <th>Capacidad</th>
          <th>Activo</th>
          <th>Notas</th>
          <th>Acciones</th>
        </tr>

        <?php
        $sql = "SELECT v.*, s.nombre AS sucursal_nombre FROM vehiculos v LEFT JOIN sucursales s ON v.sucursal_id = s.id ORDER BY v.id ASC";
        $resultado = $conn->query($sql);

        if ($resultado && $resultado->num_rows > 0) {
          while ($fila = $resultado->fetch_assoc()) {
            $id = htmlspecialchars($fila['id']);
            echo "<tr>
                    <td>" . $id . "</td>
                    <td>" . htmlspecialchars($fila['sucursal_nombre'] ?? '') . "</td>
                    <td>" . htmlspecialchars($fila['placa'] ?? '') . "</td>
                    <td>" . htmlspecialchars($fila['modelo'] ?? '') . "</td>
                    <td>" . htmlspecialchars($fila['capacidad'] ?? '') . "</td>
                    <td>" . ($fila['activo'] ? 'Sí' : 'No') . "</td>
                    <td>" . htmlspecialchars($fila['notas'] ?? '') . "</td>
                    <td class='acciones'>
                      <a href='#' class='boton-editar' onclick='alertaEditar(event, $id)'>Editar</a>
                      <a href='#' class='boton-eliminar' onclick='alertaEliminar(event, $id)'>Eliminar</a>
                    </td>
                  </tr>";
          }
        } else {
          echo "<tr><td colspan=\"8\">No hay Vehículos registrados</td></tr>";
        }
        $conn->close();
        ?>
      </table>
    </div>
  </main>

  <script>
    // Alerta al crear nuevo vehículo
    function alertaCrear(event) {
      event.preventDefault();
      Swal.fire({
        title: "¿Deseas crear un nuevo Vehículo?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Sí, crear",
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33"
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = "procesar_vehiculo.php";
        }
      });
    }

    // Alerta al editar
    function alertaEditar(event, id) {
      event.preventDefault();
      Swal.fire({
        title: "Editar Vehículo",
        text: "¿Deseas modificar la información de este vehículo?",
        icon: "info",
        showCancelButton: true,
        confirmButtonText: "Sí, editar",
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33"
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = "editar_vehiculo.php?id=" + id;
        }
      });
    }

    // Alerta al eliminar
    function alertaEliminar(event, id) {
      event.preventDefault();
      Swal.fire({
        title: "¿Estás seguro?",
        text: "Esta acción eliminará el Vehículo permanentemente.",
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
            text: "El Vehículo ha sido eliminado correctamente.",
            icon: "success",
            timer: 1500,
            showConfirmButton: false
          });
          setTimeout(() => {
            window.location.href = "eliminar_vehiculo.php?id=" + id;
          }, 1500);
        }
      });
    }
  </script>
</body>
</html>
