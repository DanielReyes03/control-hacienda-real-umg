<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario de Materias Primas</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <link rel="stylesheet" href="../../compartido/componentes/cabecera/cabecera.css">
</head>
<body>
    <?php 
        include("../../compartido/componentes/cabecera/index.php");
        cabecera("Inventario Materia Prima", '../index.php'); 
    ?>
    <div class="p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-700">Inventario de Materias Primas</h2>
            <button id="btnCrear" class="bg-[#FF9902] hover:bg-orange-600 transition-colors duration-200 text-white font-semibold py-2 px-4 rounded-lg">
                Crear Nuevo
            </button>
        </div>

        <!-- Modal -->
        <div id="modalForm" class="hidden flex inset-0 pb-12 z-50 justify-center items-center">
            <div class="bg-white p-6 rounded-lg shadow-xl w-11/12 lg:w-1/2 justify-self-center">
                <h3 id="formTitulo" class="text-lg font-bold mb-4 text-gray-700">Nueva Materia Prima</h3>

                <form id="formMateria" class="grid grid-cols-2 gap-4">
                    <input type="hidden" id="id" />
                    <div>
                        <label class="block text-sm font-semibold">Nombre</label>
                        <input id="nombre" class="w-full border p-2 rounded" required />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold">Unidad</label>
                        <input id="unidad" class="w-full border p-2 rounded" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold">Ancho</label>
                        <input id="ancho" type="number" step="0.01" class="w-full border p-2 rounded" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold">Alto</label>
                        <input id="alto" type="number" step="0.01" class="w-full border p-2 rounded" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold">Largo</label>
                        <input id="largo" type="number" step="0.01" class="w-full border p-2 rounded" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold">Costo</label>
                        <input id="costo" type="number" step="0.01" class="w-full border p-2 rounded" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold">Stock</label>
                        <input id="stock" type="number" step="0.0001" class="w-full border p-2 rounded" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold">Stock mínimo</label>
                        <input id="stock_minimo" type="number" step="0.0001" class="w-full border p-2 rounded" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold">Sucursal</label>
                        <select id="sucursal_id" class="w-full border p-2 rounded">
                            <option value="">Seleccionar sucursal...</option>
                        </select>
                    </div>
                </form>

                <div class="mt-6 flex justify-end gap-3">
                    <button id="btnGuardar" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">Guardar</button>
                    <button id="btnCancelar" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded">Cancelar</button>
                </div>
            </div>
        </div>

        <!-- Tabla -->
        <div class="overflow-x-auto shadow-lg rounded-lg">
            <table class="min-w-full border-collapse">
                <thead>
                    <tr class="bg-[#E15B65] text-white text-left">
                        <th class="px-4 py-2">Código</th>
                        <th class="px-4 py-2">Nombre</th>
                        <th class="px-4 py-2">Unidad</th>
                        <th class="px-4 py-2">Ancho</th>
                        <th class="px-4 py-2">Alto</th>
                        <th class="px-4 py-2">Largo</th>
                        <th class="px-4 py-2">Costo</th>
                        <th class="px-4 py-2">Stock</th>
                        <th class="px-4 py-2">Stock mínimo</th>
                        <th class="px-4 py-2">Sucursal</th>
                        <th class="px-4 py-2">Creado en</th>
                        <th class="px-4 py-2">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbodyMaterias" class="bg-white text-gray-700"></tbody>
            </table>
        </div>
    </div>

    <script>
        let editando = false;
        let idActual = null;

        async function cargarSucursales() {
            try {
                const res = await fetch("listar_sucursales.php");
                const sucursales = await res.json();
                const select = $("#sucursal_id");
                select.empty();
                select.append('<option value="">Seleccionar sucursal...</option>');
                sucursales.forEach((s) => select.append(`<option value="${s.id}">${s.nombre}</option>`));
            } catch (err) {
                console.error("Error al cargar sucursales:", err);
            }
        }

        async function cargarMaterias() {
            const res = await fetch("listar_materias.php");
            return await res.json();
        }

        async function renderTabla() {
            const tbody = $("#tbodyMaterias");
            tbody.empty();
            const dataMaterias = await cargarMaterias();

            if (!dataMaterias || dataMaterias.length === 0) {
                tbody.append('<tr><td colspan="12" class="text-center py-4">No hay materias primas registradas</td></tr>');
                return;
            }

            dataMaterias.forEach((m) => {
                tbody.append(`
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-2">${m.id}</td>
                        <td class="px-4 py-2">${m.nombre}</td>
                        <td class="px-4 py-2">${m.unidad}</td>
                        <td class="px-4 py-2">${m.ancho}</td>
                        <td class="px-4 py-2">${m.alto}</td>
                        <td class="px-4 py-2">${m.largo}</td>
                        <td class="px-4 py-2">${m.costo}</td>
                        <td class="px-4 py-2">${m.stock}</td>
                        <td class="px-4 py-2">${m.stock_minimo}</td>
                        <td class="px-4 py-2">${m.sucursal}</td>
                        <td class="px-4 py-2">${m.creado_en ? moment(m.creado_en).format("DD/MM/YYYY HH:SS") : ''}</td>
                        <td class="px-4 py-2">
                            <button class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded editar" data-id="${m.id}">Editar</button>
                            <button class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded eliminar" data-id="${m.id}">Eliminar</button>
                        </td>
                    </tr>
                `);
            });
        }

        // --------- GUARDAR / ACTUALIZAR ----------
        $("#btnGuardar").click(async function () {
            const params = new URLSearchParams({
                id: $("#id").val(),
                nombre: $("#nombre").val(),
                unidad: $("#unidad").val(),
                ancho: $("#ancho").val(),
                alto: $("#alto").val(),
                largo: $("#largo").val(),
                costo: $("#costo").val(),
                stock: $("#stock").val(),
                stock_minimo: $("#stock_minimo").val(),
                sucursal_id: $("#sucursal_id").val(),
            });

            const url = editando ? `actualizar_materia.php?${params.toString()}` : `crear_materia.php?${params.toString()}`;
            const res = await fetch(url);
            const msg = (await res.text()).trim();

            if (msg === "Ok") {
                alert(editando ? "Materia prima actualizada correctamente" : "Materia prima creada correctamente");
                $("#modalForm").fadeOut();
                renderTabla();
            } else {
                alert(msg);
            }
        });

        // --------- EDITAR ----------
        $(document).on("click", ".editar", async function () {
            const id = $(this).data("id");
            const data = await cargarMaterias();
            const m = data.find((x) => x.id == id);
            if (!m) return;

            idActual = id;
            editando = true;
            $("#formTitulo").text("Editar Materia Prima");

            await cargarSucursales();

            $("#id").val(m.id);
            $("#nombre").val(m.nombre);
            $("#unidad").val(m.unidad);
            $("#ancho").val(m.ancho);
            $("#alto").val(m.alto);
            $("#largo").val(m.largo);
            $("#costo").val(m.costo);
            $("#stock").val(m.stock);
            $("#stock_minimo").val(m.stock_minimo);
            $("#sucursal_id").val(m.sucursal_id);

            $("#modalForm").fadeIn();
        });

        $("#btnCrear").click(async function () {
            $("#formMateria")[0].reset();
            $("#formTitulo").text("Nueva Materia Prima");
            editando = false;
            await cargarSucursales();
            $("#modalForm").fadeIn();
        });

        $("#btnCancelar").click(function () {
            $("#modalForm").fadeOut();
        });

        // --------- ELIMINAR ----------
        $(document).on("click", ".eliminar", async function () {
            const id = $(this).data("id");

            if (!confirm("¿Seguro que deseas eliminar esta materia prima?")) return;

            const res = await fetch(`eliminar_materia.php?id=${id}`);
            const msg = (await res.text()).trim();

            if (msg === "Ok") {
                alert("Materia prima eliminada correctamente");
                renderTabla();
            } else {
                alert(msg);
            }
        });


        $(document).ready(() => renderTabla());
    </script>
</body>
</html>
