<?php
// --- check_adminEmple.php debe comprobar sesión y rol ---
require_once "../login/check_adminEmple.php"; 

// Conexión a la base de datos
include("../db/conexion.php");
$conn = conectar();

// Cabecera
include("../compartido/componentes/cabecera/index.php");
cabecera("Proveedores");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Módulo de Proveedores - Hacienda Real</title>
  <link rel="stylesheet" href="styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../compartido/componentes/cabecera/cabecera.css">
  <!-- Importar SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <main class="contenido">
    <div class="acciones">
      <a href="#" class="btn-crear" onclick="alertaCrear(event)">Crear Nuevo</a>
    </div>

    <div class="tabla-contenedor">
      <table>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Telefono</th>
          <th>Correo</th>
          <th>Producto Suministrado</th>
          <th>Dirección</th>
          <th>Origen</th>
          <th>Creado_en</th>
          <th>Acciones</th>
        </tr>

        <?php
        $sql = "SELECT * FROM proveedores ORDER BY id ASC";
        $resultado = $conn->query($sql);

        if ($resultado && $resultado->num_rows > 0) {
          while ($fila = $resultado->fetch_assoc()) {
            $id = htmlspecialchars($fila['id']);
            echo "<tr>
                    <td>{$id}</td>
                    <td>" . htmlspecialchars($fila['nombre'] ?? '') . "</td>
                    <td>" . htmlspecialchars($fila['telefono'] ?? '') . "</td>
                    <td>" . htmlspecialchars($fila['correo'] ?? '') . "</td>
                    <td>" . htmlspecialchars($fila['producto_suministra'] ?? '') . "</td>
                    <td>" . htmlspecialchars($fila['direccion'] ?? '') . "</td>
                    <td>" . htmlspecialchars($fila['origen'] ?? '') . "</td>
                    <td>" . ($fila['creado_en'] ? date('d/m/Y', strtotime($fila['creado_en'])) : '') . "</td>
                    <td class='acciones'>
                      <a href='#' class='boton-editar' onclick='alertaEditar(event, {$id})'>Editar</a>
                      <a href='#' class='boton-eliminar' onclick='alertaEliminar(event, {$id})'>Eliminar</a>
                    </td>
                  </tr>";
          }
        } else {
          echo "<tr><td colspan='9'>No hay Proveedores registrados</td></tr>";
        }
        $conn->close();
        ?>
      </table>
    </div>
  </main>

  <script>
    function alertaCrear(event) {
      event.preventDefault();
      Swal.fire({
        title: "¿Deseas crear un nuevo Proveedor?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Sí, crear",
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33"
      }).then((result) => {
        if(result.isConfirmed){
          window.location.href = "procesar_proveedor.php";
        }
      });
    }

    function alertaEditar(event, id) {
      event.preventDefault();
      Swal.fire({
        title: "Editar Proveedor",
        text: "¿Deseas modificar la información de este proveedor?",
        icon: "info",
        showCancelButton: true,
        confirmButtonText: "Sí, editar",
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33"
      }).then((result) => {
        if(result.isConfirmed){
          window.location.href = "editar_proveedores.php?id=" + id;
        }
      });
    }

    function alertaEliminar(event, id) {
      event.preventDefault();
      Swal.fire({
        title: "¿Estás seguro?",
        text: "Esta acción eliminará el Proveedor permanentemente.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6"
      }).then((result) => {
        if(result.isConfirmed){
          Swal.fire({
            title: "Eliminado",
            text: "El Proveedor ha sido eliminado correctamente.",
            icon: "success",
            timer: 1500,
            showConfirmButton: false
          });
          setTimeout(() => {
            window.location.href = "eliminar_proveedor.php?id=" + id;
          }, 1500);
        }
      });
    }
  </script>
</body>
</html>
