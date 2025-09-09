<!-- inventario.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Restaurantes - Inventario</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f7f7;
            margin: 0;
            padding: 0;
        }
        header {
            background: #333;
            color: #fff;
            padding: 15px;
            text-align: center;
        }
        h1 {
            margin: 0;
        }
        main {
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #444;
            color: white;
        }
        tr:hover {
            background: #f1f1f1;
        }
        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            color: white;
            cursor: pointer;
        }
        .btn-edit {
            background: #28a745;
        }
        .btn-delete {
            background: #dc3545;
        }
    </style>
</head>
<body>
    <header>
        <h1>Módulo de Inventario</h1>
    </header>

    <main>
        <h2>Listado de Productos</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Stock</th>
                    <th>Unidad</th>
                    <th>Precio</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Ejemplo de productos de prueba -->
                <tr>
                    <td>1</td>
                    <td>Pollo</td>
                    <td>Materia Prima</td>
                    <td>150</td>
                    <td>kg</td>
                    <td>Q25.00</td>
                    <td>
                        <button class="btn btn-edit">Editar</button>
                        <button class="btn btn-delete">Eliminar</button>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Coca Cola</td>
                    <td>Bebidas</td>
                    <td>80</td>
                    <td>unidades</td>
                    <td>Q6.00</td>
                    <td>
                        <button class="btn btn-edit">Editar</button>
                        <button class="btn btn-delete">Eliminar</button>
                    </td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Pizza Familiar</td>
                    <td>Producto Terminado</td>
                    <td>20</td>
                    <td>unidades</td>
                    <td>Q85.00</td>
                    <td>
                        <button class="btn btn-edit">Editar</button>
                        <button class="btn btn-delete">Eliminar</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </main>
</body>
</html>
