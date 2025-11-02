<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="../compartido/componentes/cabecera/cabecera.css">
    <link rel="stylesheet" href="./estilos.css">
    <title>Inventarios</title>
</head>
<body>
    <?php 
        include("../compartido/componentes/cabecera/index.php");
        cabecera("Inventarios"); 
    ?>
    <div class="flex flex-col justify-center items-center pt-12">
        <h1 class="text-xl md:text-2xl lg:text-4xl font-bold">Selecciona un inventario para gestionarlo</h1>
    </div>
    <div class="flex flex-col md:flex-row md:justify-between items-center gap-2 p-3 px-12 flex-wrap min-h-[80vh]">
        <a class="bg-[url('./assets/materia-prima.jpg')] bg-cover bg-center flex justify-center items-center w-full h-full min-w-[320px] min-h-[200px] max-w-1/4 min-h-[300px] rounded p-2 text-center text-white font-semibold shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300" 
            href="./materia-prima/index.php">
            Materia Prima
        </a>
        <a class="bg-[url('./assets/producto-terminado.jpg')] bg-cover bg-center flex justify-center items-center w-full h-full min-w-[320px] min-h-[200px] max-w-1/4 min-h-[300px] rounded p-2 text-center text-white font-semibold shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300" href="./producto-terminado/index.php">
            Producto Terminado
        </a>
        <a class="bg-[url('./assets/mobiliario-equipos.jpg')] bg-cover bg-center flex justify-center items-center w-full h-full min-w-[320px] min-h-[200px] max-w-1/4 min-h-[300px] rounded p-2 text-center text-white font-semibold shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300" href="./mobiliario-equipos/index.php">
            Mobiliario y equipos
        </a>
    </div>
</body>
</html>