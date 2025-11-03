<?php
require_once "../../login/check_adminEmple.php"; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Gestión de Productos y Recetas</title>
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
  <link rel="stylesheet" href="../../compartido/componentes/cabecera/cabecera.css">
</head>

<body class="bg-gray-100">
  <?php 
    include("../../compartido/componentes/cabecera/index.php");
    cabecera("Gestión de Productos y Recetas", '../index.php'); 
  ?>

  <div class="p-6">
    <h1 class="text-2xl font-bold text-gray-700 mb-6">Gestión de Productos y Recetas</h1>

    <!-- Tabs -->
    <div class="flex space-x-2 mb-6">
      <button class="tab-btn bg-[#E15B65] text-white px-4 py-2 rounded" data-tab="categorias">Categorías</button>
      <button class="tab-btn bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded" data-tab="productos">Productos</button>
      <button class="tab-btn bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded" data-tab="recetas">Recetas</button>
    </div>

    <!-- ================= CATEGORÍAS ================= -->
    <div id="tab-categorias" class="tab-content">
      <div class="flex justify-between mb-3">
        <h2 class="text-lg font-semibold text-gray-700">Categorías de Productos</h2>
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

    <!-- ================= PRODUCTOS ================= -->
    <div id="tab-productos" class="tab-content hidden">
      <div class="flex justify-between mb-3">
        <h2 class="text-lg font-semibold text-gray-700">Productos</h2>
        <button id="btnNuevoProducto" class="bg-[#FF9902] hover:bg-orange-600 text-white px-4 py-2 rounded">+ Nuevo Producto</button>
      </div>

      <div id="formProducto" class="hidden bg-white p-4 rounded-lg shadow mb-6">
        <h3 class="font-semibold text-gray-700 mb-2">Formulario Producto</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
          <input type="hidden" id="prodId">
          <input id="prodSku" placeholder="SKU" class="border p-2 rounded">
          <input id="prodNombre" placeholder="Nombre" class="border p-2 rounded">
          <input id="prodDescripcion" placeholder="Descripción" class="border p-2 rounded">
          <input id="prodPrecio" type="number" step="0.01" placeholder="Precio" class="border p-2 rounded">
          <select id="prodCategoriaId" class="border p-2 rounded">
            <option value="">Categoría...</option>
          </select>
          <select id="prodRecetaId" class="border p-2 rounded">
            <option value="">Receta...</option>
          </select>
          <label class="flex items-center space-x-2 col-span-2">
            <input id="prodEsItemMenu" type="checkbox" checked class="h-4 w-4 text-blue-600">
            <span>¿Es item de menú?</span>
          </label>
        </div>
        <div class="mt-4 flex justify-end gap-2">
          <button id="btnGuardarProducto" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">Guardar</button>
          <button id="btnCancelarProducto" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded">Cancelar</button>
        </div>
      </div>

      <div class="overflow-x-auto shadow rounded-lg">
        <table class="min-w-full border-collapse">
          <thead>
            <tr class="bg-[#E15B65] text-white text-left">
              <th class="px-4 py-2">ID</th>
              <th class="px-4 py-2">SKU</th>
              <th class="px-4 py-2">Nombre</th>
              <th class="px-4 py-2">Categoría</th>
              <th class="px-4 py-2">Precio</th>
              <th class="px-4 py-2">Item Menú</th>
              <th class="px-4 py-2">Acciones</th>
            </tr>
          </thead>
          <tbody id="tbodyProductos" class="bg-white text-gray-700"></tbody>
        </table>
      </div>
    </div>

    <!-- ================= RECETAS ================= -->
    <div id="tab-recetas" class="tab-content hidden">
      <div class="flex justify-between mb-3">
        <h2 class="text-lg font-semibold text-gray-700">Recetas</h2>
        <button id="btnNuevaReceta" class="bg-[#FF9902] hover:bg-orange-600 text-white px-4 py-2 rounded">+ Nueva Receta</button>
      </div>

      <!-- Formulario de Receta -->
      <div id="formReceta" class="hidden bg-white p-4 rounded-lg shadow mb-6">
        <h3 class="font-semibold text-gray-700 mb-2">Encabezado Receta</h3>
        <div class="grid grid-cols-2 gap-3 mb-4">
          <input type="hidden" id="recId">
          <input id="recNombre" placeholder="Nombre de la receta" class="border p-2 rounded">
          <input id="recDescripcion" placeholder="Descripción" class="border p-2 rounded">
        </div>

        <!-- Detalle de Receta -->
        <h3 class="font-semibold text-gray-700 mb-2">Detalle de Ingredientes</h3>
        <div class="overflow-x-auto">
          <table class="min-w-full border-collapse mb-3">
            <thead>
              <tr class="bg-gray-200 text-gray-700 text-left">
                <th class="px-3 py-2">Materia Prima</th>
                <th class="px-3 py-2">Cantidad</th>
                <th class="px-3 py-2">Acciones</th>
              </tr>
            </thead>
            <tbody id="tbodyDetalleReceta"></tbody>
          </table>
        </div>
        <button id="btnAgregarFila" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded">+ Agregar Ingrediente</button>

        <div class="mt-4 flex justify-end gap-2">
          <button id="btnGuardarReceta" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">Guardar Receta</button>
          <button id="btnCancelarReceta" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded">Cancelar</button>
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
          <tbody id="tbodyRecetas" class="bg-white text-gray-700"></tbody>
        </table>
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

    // Cargar datos según pestaña
    if (tab === "categorias") cargarCategorias();
    if (tab === "productos") cargarProductos();
    if (tab === "recetas") cargarRecetas();
    });

    // -------- Formularios toggle --------
    $("#btnNuevaCategoria").click(() => $("#formCategoria").slideToggle());
    $("#btnCancelarCategoria").click(() => $("#formCategoria").slideUp());
    $("#btnNuevoProducto").click(() => $("#formProducto").slideToggle());
    $("#btnCancelarProducto").click(() => $("#formProducto").slideUp());
    $("#btnNuevaReceta").click(() => $("#formReceta").slideToggle());
    $("#btnCancelarReceta").click(() => $("#formReceta").slideUp());

    $(document).on("click", ".eliminarFila", function () {
      $(this).closest("tr").remove();
    });

    // 🟢 Cargar Categorías
    async function cargarCategorias() {
    try {
        const res = await fetch("./categorias/listar_categorias.php");
        const data = await res.json();

        const tbody = $("#tbodyCategorias");
        tbody.empty();

        if (!data || data.length === 0) {
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

        // También cargar opciones en el select de productos
        const select = $("#prodCategoriaId");
        select.empty().append('<option value="">Categoría...</option>');
        data.forEach(c => select.append(`<option value="${c.id}">${c.nombre}</option>`));
    } catch (err) {
        console.error("Error al cargar categorías:", err);
    }
    }

    // 🟠 Cargar Productos
    async function cargarProductos() {
      try {
        const res = await fetch("./listar_productos.php");
        const data = await res.json();

        const tbody = $("#tbodyProductos");
        tbody.empty();

        if (!data || data.length === 0) {
          tbody.append(`<tr><td colspan="7" class="text-center py-4">No hay productos registrados</td></tr>`);
          return;
        }

        data.forEach(p => {
          tbody.append(`
            <tr class="border-b hover:bg-gray-50">
              <td class="px-4 py-2">${p.id}</td>
              <td class="px-4 py-2">${p.sku}</td>
              <td class="px-4 py-2">${p.nombre}</td>
              <td class="px-4 py-2">${p.categoria ?? ''}</td>
              <td class="px-4 py-2">Q ${parseFloat(p.precio).toFixed(2)}</td>
              <td class="px-4 py-2 text-center">${p.es_item_menu ? '✅' : '❌'}</td>
              <td class="px-4 py-2">
                <button class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded editarProducto" data-id="${p.id}">Editar</button>
                <button class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded eliminarProducto" data-id="${p.id}">Eliminar</button>
              </td>
            </tr>
          `);
        });
      } catch (err) {
        console.error("Error al cargar productos:", err);
      }
    }

    // 🔵 Cargar Recetas
    async function cargarRecetas() {
    try {
        const res = await fetch("./recetas/listar_recetas.php");
        const data = await res.json();

        const tbody = $("#tbodyRecetas");
        tbody.empty();

        if (!data || data.length === 0) {
        tbody.append(`<tr><td colspan="4" class="text-center py-4">No hay recetas registradas</td></tr>`);
        return;
        }

        data.forEach(r => {
        tbody.append(`
            <tr class="border-b hover:bg-gray-50 align-top">
            <td class="px-4 py-2">${r.id}</td>
            <td class="px-4 py-2">${r.nombre}</td>
            <td class="px-4 py-2">${r.descripcion ?? ''}</td>
            <td class="px-4 py-2">
                <button class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded editarReceta" data-id="${r.id}">Editar</button>
                <button class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded eliminarReceta" data-id="${r.id}">Eliminar</button>
            </td>
            </tr>
            ${r.detalle && r.detalle.length > 0 ? `
            <tr class="bg-gray-50">
                <td></td>
                <td colspan="3" class="px-6 py-2 text-sm">
                <strong>Ingredientes:</strong>
                <ul class="list-disc pl-5">
                    ${r.detalle.map(d => `<li>${d.materia_prima} (${d.cantidad})</li>`).join('')}
                </ul>
                </td>
            </tr>` : ''}
        `);
        });
    } catch (err) {
        console.error("Error al cargar recetas:", err);
    }
    }

    let materiasPrimas = [];

    async function cargarMateriasPrimas() {
      try {
        const res = await fetch("./../materia-prima/listar_materias.php");
        const data = await res.json();
        materiasPrimas = data;

        // Por si ya hay filas abiertas en recetas (edición)
        actualizarSelectsMaterias();
      } catch (err) {
        console.error("Error al cargar materias primas:", err);
      }
    }

    // Función para llenar los <select> de materias primas
    function actualizarSelectsMaterias() {
      $(".materia").each(function () {
        const select = $(this);
        const valorActual = select.val();
        select.empty();
        select.append('<option value="">Materia...</option>');
        materiasPrimas.forEach(m => {
          select.append(`<option value="${m.id}">${m.nombre}</option>`);
        });
        if (valorActual) select.val(valorActual);
      });
    }

    // Cuando se agrega una nueva fila de ingrediente
    $("#btnAgregarFila").off("click").on("click", () => {
      $("#tbodyDetalleReceta").append(`
        <tr>
          <td><select class="border p-2 rounded materia"><option value="">Materia...</option></select></td>
          <td><input type="number" step="0.0001" class="border p-2 rounded cantidad" placeholder="Cantidad"></td>
          <td><button class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded eliminarFila">X</button></td>
        </tr>
      `);
      actualizarSelectsMaterias();
    });


    //---------------------------------------------------------
    // 🟢 CRUD CATEGORÍAS
    //---------------------------------------------------------
    $("#btnGuardarCategoria").click(async function () {
      const id = $("#catId").val();
      const nombre = $("#catNombre").val().trim();
      const descripcion = $("#catDescripcion").val().trim();

      if (!nombre) return alert("El nombre es obligatorio");

      const params = new URLSearchParams({ nombre, descripcion });
      let url = "";

      if (id) {
        params.append("id", id);
        url = `./categorias/actualizar_categoria.php?${params.toString()}`;
      } else {
        url = `./categorias/crear_categoria.php?${params.toString()}`;
      }

      const res = await fetch(url);
      const msg = (await res.text()).trim();
      if (msg === "Ok") {
        alert(id ? "Categoría actualizada correctamente" : "Categoría creada correctamente");
        $("#formCategoria").slideUp();
        $("#catId").val("");
        $("#catNombre").val("");
        $("#catDescripcion").val("");
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


    //---------------------------------------------------------
    // 🟠 CRUD PRODUCTOS
    //---------------------------------------------------------
    $("#btnGuardarProducto").click(async function () {
      const id = $("#prodId").val();
      const params = new URLSearchParams({
        categoria_id: $("#prodCategoriaId").val(),
        sku: $("#prodSku").val().trim(),
        nombre: $("#prodNombre").val().trim(),
        descripcion: $("#prodDescripcion").val().trim(),
        precio: $("#prodPrecio").val(),
        es_item_menu: $("#prodEsItemMenu").is(":checked"),
        receta_id: $("#prodRecetaId").val()
      });

      if (!params.get("nombre")) return alert("El nombre es obligatorio");
      if (!params.get("sku")) return alert("El SKU es obligatorio");
      if (!params.get("categoria_id")) return alert("Selecciona una categoría");
      if (!params.get("receta_id")) return alert("Selecciona una receta");

      let url = "";
      if (id) {
        params.append("id", id);
        url = `./actualizar_producto.php?${params.toString()}`;
      } else {
        url = `./crear_producto.php?${params.toString()}`;
      }

      const res = await fetch(url);
      const msg = (await res.text()).trim();
      if (msg === "Ok") {
        alert(id ? "Producto actualizado correctamente" : "Producto creado correctamente");
        $("#formProducto").slideUp();
        $("#formProducto input, #formProducto select").val("");
        $("#prodEsItemMenu").prop("checked", true);
        cargarProductos();
      } else alert(msg);
    });

    // Editar producto
    $(document).on("click", ".editarProducto", async function () {
      const id = $(this).data("id");
      const res = await fetch("./listar_productos.php");
      const data = await res.json();
      const p = data.find(x => x.id == id);
      if (!p) return;

      $("#prodId").val(p.id);
      $("#prodSku").val(p.sku);
      $("#prodNombre").val(p.nombre);
      $("#prodDescripcion").val(p.descripcion);
      $("#prodPrecio").val(p.precio);
      $("#prodCategoriaId").val(p.categoria_id);
      $("#prodRecetaId").val(p.receta_id);
      $("#prodEsItemMenu").prop("checked", p.es_item_menu);
      $("#formProducto").slideDown();
    });

    // Eliminar producto
    $(document).on("click", ".eliminarProducto", async function () {
      const id = $(this).data("id");
      if (!confirm("¿Seguro que deseas eliminar este producto?")) return;
      const res = await fetch(`./eliminar_producto.php?id=${id}`);
      const msg = (await res.text()).trim();
      if (msg === "Ok") {
        alert("Producto eliminado correctamente");
        cargarProductos();
      } else alert(msg);
    });

    //---------------------------------------------------------
    // 🔵 CRUD RECETAS
    //---------------------------------------------------------
    $("#btnGuardarReceta").click(async function () {
      const id = $("#recId").val();
      const nombre = $("#recNombre").val().trim();
      const descripcion = $("#recDescripcion").val().trim();

      if (!nombre) return alert("El nombre de la receta es obligatorio");

      // Construir detalle
      const detalle = [];
      $("#tbodyDetalleReceta tr").each(function () {
        const materia_id = $(this).find(".materia").val();
        const cantidad = $(this).find(".cantidad").val();
        if (materia_id && cantidad) detalle.push({ materia_prima_id: materia_id, cantidad });
      });

      if (detalle.length === 0) return alert("Agrega al menos un ingrediente");

      const params = new URLSearchParams({
        nombre,
        descripcion,
        detalle: JSON.stringify(detalle)
      });

      let url = "";
      if (id) {
        params.append("id", id);
        url = `./recetas/actualizar_receta.php?${params.toString()}`;
      } else {
        url = `./recetas/crear_receta.php?${params.toString()}`;
      }

      const res = await fetch(url);
      const msg = (await res.text()).trim();
      if (msg === "Ok") {
        alert(id ? "Receta actualizada correctamente" : "Receta creada correctamente");
        $("#formReceta").slideUp();
        $("#recId, #recNombre, #recDescripcion").val("");
        $("#tbodyDetalleReceta").empty();
        cargarRecetas();
      } else alert(msg);
    });

  // Editar receta
    $(document).on("click", ".editarReceta", async function () {
      const id = $(this).data("id");
      const res = await fetch("./recetas/listar_recetas.php");
      const data = await res.json();
      const r = data.find(x => x.id == id);
      if (!r) return;

      $("#recId").val(r.id);
      $("#recNombre").val(r.nombre);
      $("#recDescripcion").val(r.descripcion);
      $("#tbodyDetalleReceta").empty();

      r.detalle.forEach(d => {
        $("#tbodyDetalleReceta").append(`
          <tr>
            <td><select class="border p-2 rounded materia"></select></td>
            <td><input type="number" step="0.0001" class="border p-2 rounded cantidad" value="${d.cantidad}"></td>
            <td><button class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded eliminarFila">X</button></td>
          </tr>
        `);
      });

      actualizarSelectsMaterias();

      // Establecer valor actual de materia prima en cada fila
      $("#tbodyDetalleReceta tr").each(function (index) {
        const item = r.detalle[index];
        $(this).find(".materia").val(item.materia_prima_id);
      });

      $("#formReceta").slideDown();
    });

    // Eliminar receta
    $(document).on("click", ".eliminarReceta", async function () {
      const id = $(this).data("id");
      if (!confirm("¿Seguro que deseas eliminar esta receta?")) return;
      const res = await fetch(`./recetas/eliminar_receta.php?id=${id}`);
      const msg = (await res.text()).trim();
      if (msg === "Ok") {
        alert("Receta eliminada correctamente");
        cargarRecetas();
      } else alert(msg);
    });

    //---------------------------------------------------------
    // 🚫 Validar que no se repitan materias primas en la receta
    //---------------------------------------------------------
    $(document).on("change", ".materia", function () {
      const seleccionActual = $(this).val();

      if (!seleccionActual) return;

      // Contar cuántas veces aparece ese id en los selects
      const repetidos = $(".materia").filter(function () {
        return $(this).val() === seleccionActual;
      });

      if (repetidos.length > 1) {
        // Mostrar alerta
        alert("⚠️ Ya has agregado esta materia prima a la receta.");

        // Quitar la selección duplicada
        $(this).val("");

        // Efecto visual de error
        $(this).addClass("border-red-500 bg-red-100");
        setTimeout(() => $(this).removeClass("border-red-500 bg-red-100"), 1200);
      }
    });


    // 🔵 Cargar recetas para el select de productos
    async function cargarRecetasSelect() {
      try {
        const selectRec = $("#prodRecetaId");
        selectRec.empty().append('<option value="">Receta...</option>');
        const resRec = await fetch("./recetas/listar_recetas.php");
        const recetas = await resRec.json();
        if (Array.isArray(recetas) && recetas.length > 0) {
          recetas.forEach(r => {
            selectRec.append(`<option value="${r.id}">${r.nombre}</option>`);
          });
        } else {
          selectRec.append('<option value="">No hay recetas registradas</option>');
        }
      } catch (err) {
        console.error("Error al cargar recetas en el select:", err);
      }
    }


    $(document).ready(async () => {
      $("#tab-categorias").show();
      await cargarCategorias();
      await cargarRecetas();      // para la tabla de recetas
      await cargarRecetasSelect(); // 👈 para llenar el select del formulario
      await cargarProductos();
      await cargarMateriasPrimas();
    });

  </script>
</body>
</html>
