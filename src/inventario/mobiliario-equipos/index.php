<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Gestión de Mobiliario y Equipos</title>
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link rel="stylesheet" href="../../compartido/componentes/cabecera/cabecera.css">
</head>

<body class="bg-gray-100">
  <?php 
    include("../../compartido/componentes/cabecera/index.php");
    cabecera("Gestión de Mobiliario y Equipos", '../index.php'); 
  ?>

  <div class="p-6">
    <h1 class="text-2xl font-bold text-gray-700 mb-6">Gestión de Mobiliario y Equipos</h1>

    <!-- Tabs -->
    <div class="flex space-x-2 mb-6">
      <button class="tab-btn bg-[#E15B65] text-white px-4 py-2 rounded" data-tab="categorias">Categorías</button>
      <button class="tab-btn bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded" data-tab="activos">Activos</button>
      <button class="tab-btn bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded" data-tab="movimientos">Movimientos</button>
    </div>

    <!-- ================= CATEGORÍAS ================= -->
    <div id="tab-categorias" class="tab-content">
      <div class="flex justify-between mb-3">
        <h2 class="text-lg font-semibold text-gray-700">Categorías de Activos</h2>
        <button id="btnNuevaCategoria" class="bg-[#FF9902] hover:bg-orange-600 text-white px-4 py-2 rounded">+ Nueva Categoría</button>
      </div>

      <div id="formCategoria" class="hidden bg-white p-4 rounded-lg shadow mb-6">
        <h3 class="font-semibold text-gray-700 mb-2">Formulario Categoría</h3>
        <div class="grid grid-cols-2 gap-3">
          <input type="hidden" id="catId">
          <input id="catNombre" placeholder="Nombre" class="border p-2 rounded">
          <input id="catDescripcion" placeholder="Descripción" class="border p-2 rounded">
        </div>
        <div class="mt-4 flex justify-end gap-2">
          <button id="btnGuardarCategoria" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">Guardar</button>
          <button id="btnCancelarCategoria" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded">Cancelar</button>
        </div>
      </div>

      <div class="overflow-x-auto shadow rounded-lg">
        <table class="min-w-full border-collapse">
          <thead>
            <tr class="bg-[#E15B65] text-white text-left">
              <th class="px-4 py-2">ID</th>
              <th class="px-4 py-2">Nombre</th>
              <th class="px-4 py-2">Descripción</th>
              <th class="px-4 py-2">Acciones</th>
            </tr>
          </thead>
          <tbody id="tbodyCategorias" class="bg-white text-gray-700"></tbody>
        </table>
      </div>
    </div>

    <!-- ================= ACTIVOS ================= -->
    <div id="tab-activos" class="tab-content hidden">
      <div class="flex justify-between mb-3">
        <h2 class="text-lg font-semibold text-gray-700">Activos</h2>
        <button id="btnNuevoActivo" class="bg-[#FF9902] hover:bg-orange-600 text-white px-4 py-2 rounded">+ Nuevo Activo</button>
      </div>

      <div id="formActivo" class="hidden bg-white p-4 rounded-lg shadow mb-6">
        <h3 class="font-semibold text-gray-700 mb-2">Formulario Activo</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
          <input type="hidden" id="activoId">
          <input id="activoCodigo" placeholder="Código interno" class="border p-2 rounded">
          <input id="activoNombre" placeholder="Nombre" class="border p-2 rounded">
          <input id="activoMarca" placeholder="Marca" class="border p-2 rounded">
          <input id="activoModelo" placeholder="Modelo" class="border p-2 rounded">
          <input id="activoSerie" placeholder="Serie" class="border p-2 rounded">
          <input id="activoCosto" type="number" step="0.01" placeholder="Costo" class="border p-2 rounded">
          <input id="activoFecha" type="date" class="border p-2 rounded">
          <select id="activoCategoria" class="border p-2 rounded"><option value="">Categoría...</option></select>
          <select id="activoSucursal" class="border p-2 rounded"><option value="">Sucursal...</option></select>
          <select id="activoEstado" class="border p-2 rounded">
            <option value="activo">Activo</option>
            <option value="en_reparacion">En reparación</option>
            <option value="baja">Baja</option>
          </select>
        </div>
        <div class="mt-4 flex justify-end gap-2">
          <button id="btnGuardarActivo" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">Guardar</button>
          <button id="btnCancelarActivo" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded">Cancelar</button>
        </div>
      </div>

      <div class="overflow-x-auto shadow rounded-lg">
        <table class="min-w-full border-collapse">
        <thead>
          <tr class="bg-[#E15B65] text-white text-left">
            <th class="px-4 py-2">Código</th>
            <th class="px-4 py-2">Nombre</th>
            <th class="px-4 py-2">Marca</th>
            <th class="px-4 py-2">Modelo</th>
            <th class="px-4 py-2">Serie</th>
            <th class="px-4 py-2">Categoría</th>
            <th class="px-4 py-2">Sucursal</th>
            <th class="px-4 py-2">Costo</th>
            <th class="px-4 py-2">Fecha Adquisición</th>
            <th class="px-4 py-2">Estado</th>
            <th class="px-4 py-2">Acciones</th>
          </tr>
        </thead>
          <tbody id="tbodyActivos" class="bg-white text-gray-700"></tbody>
        </table>
      </div>
    </div>

    <!-- ================= MOVIMIENTOS ================= -->
        <div id="tab-movimientos" class="tab-content hidden">
          <div class="flex justify-between mb-3">
            <h2 class="text-lg font-semibold text-gray-700">Historial de Movimientos</h2>
            <button id="btnNuevoMovimiento" class="bg-[#FF9902] hover:bg-orange-600 text-white px-4 py-2 rounded">+ Nuevo Movimiento</button>
          </div>

    <!-- Formulario Movimiento -->
    <div id="formMovimiento" class="hidden bg-white p-4 rounded-lg shadow mb-6">
      <h3 class="font-semibold text-gray-700 mb-2">Registrar Movimiento</h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <select id="movActivo" class="border p-2 rounded"><option value="">Activo...</option></select>
        <select id="movTipo" class="border p-2 rounded">
          <option value="">Tipo de movimiento...</option>
          <option value="traslado">Traslado</option>
          <option value="mantenimiento">Mantenimiento</option>
          <option value="asignacion">Asignación</option>
          <option value="baja">Baja</option>
          <option value="alta">Alta</option>
        </select>
        <div id="destinoContainer" class="hidden">
          <select id="movDestino" class="border p-2 rounded w-full">
            <option value="">Destino (sucursal)...</option>
          </select>
        </div>
        <input id="movObservaciones" placeholder="Observaciones" class="border p-2 rounded col-span-1 md:col-span-3">
      </div>
      <div class="mt-4 flex justify-end gap-2">
        <button id="btnGuardarMovimiento" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">Guardar</button>
        <button id="btnCancelarMovimiento" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded">Cancelar</button>
      </div>
    </div>


          <!-- Tabla Movimientos -->
          <div class="overflow-x-auto shadow rounded-lg">
            <table class="min-w-full border-collapse">
              <thead>
                <tr class="bg-[#E15B65] text-white text-left">
                  <th class="px-4 py-2">Activo</th>
                  <th class="px-4 py-2">Tipo</th>
                  <th class="px-4 py-2">Origen</th>
                  <th class="px-4 py-2">Destino</th>
                  <th class="px-4 py-2">Fecha</th>
                  <th class="px-4 py-2">Observaciones</th>
                </tr>
              </thead>
              <tbody id="tbodyMovimientos" class="bg-white text-gray-700"></tbody>
            </table>
          </div>
        </div>
      </div>

  </div>

<script>
  // -------- Tabs --------
  $(".tab-btn").click(function () {
    const tab = $(this).data("tab");
    $(".tab-btn").removeClass("bg-[#E15B65] text-white").addClass("bg-gray-300");
    $(this).removeClass("bg-gray-300").addClass("bg-[#E15B65] text-white");
    $(".tab-content").hide();
    $(`#tab-${tab}`).show();

    if (tab === "categorias") cargarCategorias();
    if (tab === "activos") { cargarCategorias(); cargarSucursales(); cargarActivos(); }
    if (tab === "movimientos") { cargarMovimientos(); cargarActivosSelect(); cargarSucursalesMov(); }
  });

  // -------- Formularios toggle --------
  $("#btnNuevaCategoria").click(() => $("#formCategoria").slideToggle());
  $("#btnCancelarCategoria").click(() => $("#formCategoria").slideUp());
  $("#btnNuevoActivo").click(() => $("#formActivo").slideToggle());
  $("#btnCancelarActivo").click(() => {
    console.log('Cancelar activo');
    
    limpiarFormularioActivo();
    $("#formActivo").slideUp();
  });


  function limpiarFormularioActivo() {
  $("#activoId").val("");
  $("#formActivo input[type='text'], #formActivo input[type='number'], #formActivo input[type='date']").val("");
  $("#formActivo select").val("");
}


  // ===================================================
  // 🟢 CRUD CATEGORÍAS
  // ===================================================
  async function cargarCategorias() {
    const res = await fetch("./categorias/listar_categorias.php");
    const data = await res.json();
    const tbody = $("#tbodyCategorias");
    tbody.empty();

    if (!data.length) {
      tbody.append(`<tr><td colspan="4" class="text-center py-4">No hay categorías registradas</td></tr>`);
      return;
    }

    data.forEach(c => {
      tbody.append(`
        <tr class="border-b hover:bg-gray-50">
          <td class="px-4 py-2">${c.id}</td>
          <td class="px-4 py-2">${c.nombre}</td>
          <td class="px-4 py-2">${c.descripcion ?? ''}</td>
          <td class="px-4 py-2">
            <button class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded editarCategoria" data-id="${c.id}">Editar</button>
            <button class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded eliminarCategoria" data-id="${c.id}">Eliminar</button>
          </td>
        </tr>
      `);
    });

    // llenar select de categorías en activos
    const selectCat = $("#activoCategoria");
    selectCat.empty().append('<option value="">Categoría...</option>');
    data.forEach(c => selectCat.append(`<option value="${c.id}">${c.nombre}</option>`));
  }

  // Guardar o actualizar categoría
  $("#btnGuardarCategoria").click(async function () {
    const id = $("#catId").val();
    const nombre = $("#catNombre").val().trim();
    const descripcion = $("#catDescripcion").val().trim();
    if (!nombre) return alert("El nombre es obligatorio");

    const params = new URLSearchParams({ nombre, descripcion });
    let url = id
      ? `./categorias/actualizar_categoria.php?${params.toString()}&id=${id}`
      : `./categorias/crear_categoria.php?${params.toString()}`;

    const res = await fetch(url);
    const msg = (await res.text()).trim();
    if (msg === "Ok") {
      alert(id ? "Categoría actualizada correctamente" : "Categoría creada correctamente");
      $("#formCategoria").slideUp();
      $("#catId,#catNombre,#catDescripcion").val("");

      cargarCategorias();
    } else alert(msg);
  });

  // Editar categoría
  $(document).on("click", ".editarCategoria", function () {
    const tr = $(this).closest("tr");
    $("#catId").val($(this).data("id"));
    $("#catNombre").val(tr.find("td:eq(1)").text());
    $("#catDescripcion").val(tr.find("td:eq(2)").text());
    $("#formCategoria").slideDown();
  });

  // Eliminar categoría
  $(document).on("click", ".eliminarCategoria", async function () {
    const id = $(this).data("id");
    if (!confirm("¿Seguro que deseas eliminar esta categoría?")) return;
    const res = await fetch(`./categorias/eliminar_categoria.php?id=${id}`);
    const msg = (await res.text()).trim();
    if (msg === "Ok") {
      alert("Categoría eliminada correctamente");
      cargarCategorias();
    } else alert(msg);
  });

  // ===================================================
  // 🟠 CRUD ACTIVOS
  // ===================================================
  async function cargarSucursales() {
    const res = await fetch("../../inventario/materia-prima/listar_sucursales.php");
    const data = await res.json();
    const select = $("#activoSucursal");
    select.empty().append('<option value="">Sucursal...</option>');
    data.forEach(s => select.append(`<option value="${s.id}">${s.nombre}</option>`));
  }

  async function cargarActivos() {
    const res = await fetch("./listar_activos.php");
    const data = await res.json();
    const tbody = $("#tbodyActivos");
    tbody.empty();

    if (!data.length) {
      tbody.append(`<tr><td colspan="6" class="text-center py-4">No hay activos registrados</td></tr>`);
      return;
    }

    data.forEach(a => {
      tbody.append(`
        <tr class="border-b hover:bg-gray-50">
          <td class="px-4 py-2">${a.codigo_interno}</td>
          <td class="px-4 py-2">${a.nombre}</td>
          <td class="px-4 py-2">${a.marca ?? ''}</td>
          <td class="px-4 py-2">${a.modelo ?? ''}</td>
          <td class="px-4 py-2">${a.serie ?? ''}</td>
          <td class="px-4 py-2">${a.categoria ?? ''}</td>
          <td class="px-4 py-2">${a.sucursal ?? ''}</td>
          <td class="px-4 py-2">Q ${parseFloat(a.costo).toFixed(2)}</td>
          <td class="px-4 py-2">${a.fecha_adquisicion ?? ''}</td>
          <td class="px-4 py-2">${a.estado}</td>
          <td class="px-4 py-2">
            <button class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded editarActivo" data-id="${a.id}">Editar</button>
            <button class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded eliminarActivo" data-id="${a.id}">Eliminar</button>
          </td>
        </tr>
      `);
    });
  }

  // Guardar o actualizar activo
  $("#btnGuardarActivo").click(async function () {
    const id = $("#activoId").val();
    const params = new URLSearchParams({
      categoria_id: $("#activoCategoria").val(),
      sucursal_id: $("#activoSucursal").val(),
      codigo_interno: $("#activoCodigo").val().trim(),
      nombre: $("#activoNombre").val().trim(),
      marca: $("#activoMarca").val().trim(),
      modelo: $("#activoModelo").val().trim(),
      serie: $("#activoSerie").val().trim(),
      costo: $("#activoCosto").val(),
      fecha_adquisicion: $("#activoFecha").val(),
      estado: $("#activoEstado").val(),
      descripcion: ""
    });

    if (!params.get("categoria_id")) return alert("Selecciona una categoría");
    if (!params.get("sucursal_id")) return alert("Selecciona una sucursal");
    if (!params.get("codigo_interno")) return alert("El código es obligatorio");
    if (!params.get("nombre")) return alert("El nombre es obligatorio");

    const url = id
      ? `./actualizar_activo.php?${params.toString()}&id=${id}`
      : `./crear_activo.php?${params.toString()}`;

    const res = await fetch(url);
    const msg = (await res.text()).trim();
    if (msg === "Ok") {
      alert(id ? "Activo actualizado correctamente" : "Activo creado correctamente");
      limpiarFormularioActivo();
      $("#formActivo").slideUp();
      cargarActivos();
    } else alert(msg);
  });

  // Editar activo
  $(document).on("click", ".editarActivo", async function () {
    const id = $(this).data("id");
    const res = await fetch("./listar_activos.php");
    const data = await res.json();
    const a = data.find(x => x.id == id);
    if (!a) return;

    $("#activoId").val(a.id);
    $("#activoCodigo").val(a.codigo_interno);
    $("#activoNombre").val(a.nombre);
    $("#activoMarca").val(a.marca);
    $("#activoModelo").val(a.modelo);
    $("#activoSerie").val(a.serie);
    $("#activoCosto").val(a.costo);
    $("#activoFecha").val(a.fecha_adquisicion);
    $("#activoCategoria").val(a.categoria_id);
    $("#activoSucursal").val(a.sucursal_id);
    $("#activoEstado").val(a.estado);
    $("#formActivo").slideDown();
  });

  // Eliminar activo
  $(document).on("click", ".eliminarActivo", async function () {
    const id = $(this).data("id");
    if (!confirm("¿Seguro que deseas eliminar este activo?")) return;
    const res = await fetch(`./eliminar_activo.php?id=${id}`);
    const msg = (await res.text()).trim();
    if (msg === "Ok") {
      alert("Activo eliminado correctamente");
      cargarActivos();
    } else alert(msg);
  });

  // ===================================================
// 🔵 CRUD MOVIMIENTOS
// ===================================================

// Mostrar / ocultar formulario
$("#btnNuevoMovimiento").on("click", async function () {
  const form = $("#formMovimiento");
  const isHidden = form.hasClass("hidden");

  limpiarFormularioMovimiento();

  if (isHidden) {
    // Cargar datos cada vez que se abre
    await Promise.all([cargarActivosSelect(), cargarSucursalesMov()]);
    form.removeClass("hidden");
  } else {
    form.addClass("hidden");
  }
});

$("#btnCancelarMovimiento").on("click", function () {
  limpiarFormularioMovimiento();
  $("#formMovimiento").addClass("hidden");
});

// Limpiar formulario
function limpiarFormularioMovimiento() {
  $("#formMovimiento").find("input, select").val("");
  $("#destinoContainer").addClass("hidden");
}

// Mostrar destino solo para traslado / alta
$("#movTipo").on("change", function () {
  const tipo = $(this).val();
  if (tipo === "traslado" || tipo === "alta" || tipo === "asignacion") {
    $("#destinoContainer").removeClass("hidden");
  } else {
    $("#destinoContainer").addClass("hidden");
    $("#movDestino").val("");
  }
});

// Cargar selects
async function cargarActivosSelect() {
  try {
    const res = await fetch("./listar_activos.php");
    const data = await res.json();
    const select = $("#movActivo");
    select.empty().append('<option value="">Activo...</option>');
    data.forEach(a => {
      select.append(`<option value="${a.id}">${a.codigo_interno} - ${a.nombre}</option>`);
    });
  } catch (err) {
    console.error("Error al cargar activos:", err);
  }
}

async function cargarSucursalesMov() {
  try {
    const res = await fetch("../../inventario/materia-prima/listar_sucursales.php");
    const data = await res.json();
    const select = $("#movDestino");
    select.empty().append('<option value="">Destino (sucursal)...</option>');
    data.forEach(s => select.append(`<option value="${s.id}">${s.nombre}</option>`));
  } catch (err) {
    console.error("Error al cargar sucursales:", err);
  }
}

// Guardar movimiento
$("#btnGuardarMovimiento").on("click", async function () {
  const activo_id = $("#movActivo").val();
  const tipo_movimiento = $("#movTipo").val();
  const destino_id = $("#movDestino").val();
  const observaciones = $("#movObservaciones").val().trim();

  if (!activo_id || !tipo_movimiento)
    return alert("Selecciona un activo y un tipo de movimiento.");

  const params = new URLSearchParams({
    activo_id,
    tipo_movimiento,
    destino_id,
    observaciones
  });

  try {
    const res = await fetch(`./movimientos/crear_movimiento.php?${params.toString()}`);
    const msg = (await res.text()).trim();

    if (msg === "Ok") {
      alert("Movimiento registrado correctamente");
      limpiarFormularioMovimiento();
      $("#formMovimiento").addClass("hidden");
      cargarMovimientos();
    } else {
      alert(msg);
    }
  } catch (err) {
    console.error("Error al guardar movimiento:", err);
  }
});

// Listar movimientos
async function cargarMovimientos() {
  const res = await fetch("./movimientos/listar_movimientos.php");
  const data = await res.json();
  const tbody = $("#tbodyMovimientos");
  tbody.empty();

  if (!data.length) {
    tbody.append(`<tr><td colspan="6" class="text-center py-4">No hay movimientos registrados</td></tr>`);
    return;
  }

  data.forEach(m => {
    tbody.append(`
      <tr class="border-b hover:bg-gray-50">
        <td class="px-4 py-2">${m.activo}</td>
        <td class="px-4 py-2">${m.tipo_movimiento}</td>
        <td class="px-4 py-2">${m.origen ?? ''}</td>
        <td class="px-4 py-2">${m.destino ?? ''}</td>
        <td class="px-4 py-2">${m.fecha_movimiento}</td>
        <td class="px-4 py-2">${m.observaciones ?? ''}</td>
      </tr>
    `);
  });
}


  // ===================================================
  // 🚀 INICIALIZACIÓN
  // ===================================================
  $(document).ready(() => {
    $("#tab-categorias").show();
    cargarCategorias();
    cargarSucursales();
    cargarActivosSelect();
    cargarSucursalesMov();
  });
</script>

</body>
</html>
