<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hacienda Real - Información</title>

  <!-- Tailwind -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- SwiperJS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css" />

  <!-- Cabecera global -->
  <link rel="stylesheet" href="../compartido/componentes/cabecera/cabecera.css">

  <!-- CSS personal -->
  <link rel="stylesheet" href="style_hacienda.css">

  <style>
    html { scroll-behavior: smooth; }

    /* Fondo seccion especial */
    .special-bg {
      background:
        linear-gradient(rgba(0,0,0,.45), rgba(0,0,0,.45)),
        url('images/carne_fondo.jpg') center/cover no-repeat;
    }

    /* Imagen Quienes Somos */
    .quienes-img {
      background: url('imagenes./chef.jpeg') center/cover no-repeat;
    }
  </style>
</head>

<body class="text-gray-800">


<!--  MENÚ SUPERIOR -->
<nav class="fixed top-0 left-0 w-full bg-black bg-opacity-70 backdrop-blur-sm z-50">
  <div class="max-w-7xl mx-auto px-6">
    <div class="flex items-center justify-between h-16">

      <!-- LOGO -->
      <img src="imagenes/logo.png" alt="Logo HR" class="h-10">

      <!-- MENÚ -->
      <ul class="flex space-x-10 text-white tracking-widest text-sm font-semibold">
        <li><a href="#inicio" class="hover:text-[#FFD700] transition">INICIO</a></li>
        <li><a href="#quienes" class="hover:text-[#FFD700] transition">QUIÉNES SOMOS</a></li>
        <li><a href="#sucursales" class="hover:text-[#FFD700] transition">SUCURSALES</a></li>
      </ul>

      <!-- BOTÓN LOGIN -->
      <a href="../login/login.php" 
         class="px-4 py-2 bg-[#8B0000] text-white rounded-md hover:bg-[#A40000] transition font-semibold">
         Iniciar Sesión
      </a>

    </div>
  </div>
</nav>

<div id="inicio"></div>

<!-- Espacio por menú fijo -->
<div class="h-16"></div>


<!--  HERO -->
<section 
  class="relative w-full h-[360px] flex items-center justify-center text-white shadow-xl"
  style="
    background:
      linear-gradient(rgba(0,0,0,0.45), rgba(0,0,0,0.45)),
      url('imagenes/hacienda.jpg') center/cover no-repeat;
  "
>
  <div class="text-center max-w-2xl px-6 z-20">
    <h1 class="text-5xl font-extrabold mb-3 drop-shadow-lg">Hacienda Real Guatemala</h1>
    <p class="text-xl font-light drop-shadow-md">
      Tradición, sabor y hospitalidad desde 1977.
    </p>
  </div>

</section>




<!--  CARRUSEL -->
<section class="relative w-full h-[440px] overflow-hidden mt-10">

  <div class="swiper mySwiper h-full">
    <div class="swiper-wrapper">

      <div class="swiper-slide"><img src="imagenes/fondo1.jpeg" class="w-full h-full object-cover"></div>
      <div class="swiper-slide"><img src="imagenes/Parrillada.webp" class="w-full h-full object-cover"></div>
      <div class="swiper-slide"><img src="imagenes/tacos.jpg" class="w-full h-full object-cover"></div>
      <div class="swiper-slide"><img src="imagenes/cayala.jpg" class="w-full h-full object-cover"></div>

    </div>

    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>
    <div class="swiper-pagination"></div>

  </div>

</section>



<section 
  class="relative w-full h-[380px] flex items-center justify-center text-white mt-16"
  style="
    background:
      linear-gradient(rgba(0,0,0,0.45), rgba(0,0,0,0.45)),
      url('imagenes/cayala.jpg') center/cover no-repeat;
  "
>
  <div class="text-center max-w-3xl px-6 z-20">
    <h2 class="text-4xl font-bold drop-shadow-lg mb-3">Nuestra Pasión por la Parrilla</h2>
    <p class="text-lg font-light drop-shadow-md leading-relaxed">
      Cada corte, cada sazón y cada receta es un tributo al sabor tradicional.
      Cocinamos con dedicación, técnicas de fuego lento y pasión por compartir
      la esencia gastronómica de Guatemala.
    </p>
  </div>

</section>





<!--  QUIÉNES SOMOS -->
<div id="quienes"></div>

<section class="max-w-6xl mx-auto mt-20 p-10 bg-white card-premium">

  <h2 class="title-section">¿Quiénes Somos?</h2>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mt-10">

    <!-- Imagen -->
    <div class="w-full h-[380px] md:h-[420px] rounded-xl shadow-lg overflow-hidden">
      <img src="imagenes/chef.jpeg" class="w-full h-full object-cover object-center">
    </div>

    <!-- Texto -->
    <div>
      <p class="text-content mb-4">
        Hacienda Real nació con una visión clara: preservar los auténticos sabores
        de la cocina guatemalteca y fusionarlos con cortes premium preparados a las brasas.
      </p>

      <p class="text-content mb-4">
        Nuestra historia comenzó hace más de cuatro décadas, cuando abrimos nuestro
        primer local con la ilusión de ofrecer algo especial: un lugar donde la tradición,
        el ambiente familiar y la excelencia culinaria se unieran.
      </p>

      <p class="text-content mb-4">
        Hoy continuamos innovando, manteniendo siempre nuestros valores:
        calidad, servicio, hospitalidad y respeto por las raíces que nos representan.
      </p>

      <p class="text-content mb-4">
        Cada plato cuenta una historia. Cada visita crea un recuerdo.
        En Hacienda Real, lo más importante eres tú.
      </p>
    </div>

  </div>
</section>



<!--  SUCURSALES -->
<div id="sucursales"></div>

<section class="max-w-7xl mx-auto mt-20 p-10 bg-white card-premium">

  <h2 class="title-section">Sucursales</h2>

  <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-10 mt-10">

    <div class="card-premium overflow-hidden">
      <div class="h-40 bg-center bg-cover" style="background-image:url('imagenes/zona\ 10.png');"></div>
      <div class="p-6">
        <h3 class="text-2xl font-bold text-[#8B0000]">Zona 10 (Sede Principal)</h3>
        <p><b>Dirección:</b> 5ta Avenida 14-67, Zona 10</p>
        <p><b>Teléfono:</b> +502 2380-8383</p>
      </div>
    </div>

    <div class="card-premium overflow-hidden">
      <div class="h-40 bg-center bg-cover" style="background-image:url('imagenes/majadas.jpg');"></div>
      <div class="p-6">
        <h3 class="text-2xl font-bold text-[#8B0000]">Zona 11 (Las Majadas)</h3>
        <p><b>Dirección:</b> Anillo Periférico, Las Majadas</p>
        <p><b>Teléfono:</b> +502 2473-7070</p>
      </div>
    </div>

    <div class="card-premium overflow-hidden">
      <div class="h-40 bg-center bg-cover" style="background-image:url('imagenes/zona14.jpg');"></div>
      <div class="p-6">
        <h3 class="text-2xl font-bold text-[#8B0000]">Zona 14</h3>
        <p><b>Dirección:</b> Boulevard Vista Hermosa, Zona 14</p>
        <p><b>Teléfono:</b> +502 2380-8383</p>
      </div>
    </div>

    <div class="card-premium overflow-hidden">
      <div class="h-40 bg-center bg-cover" style="background-image:url('imagenes/condado.jpg');"></div>
      <div class="p-6">
        <h3 class="text-2xl font-bold text-[#8B0000]">Condado Concepción</h3>
        <p><b>Dirección:</b> Km 15.5 Carretera a El Salvador</p>
        <p><b>Teléfono:</b> +502 6636-0000</p>
      </div>
    </div>

    <div class="card-premium overflow-hidden">
      <div class="h-40 bg-center bg-cover" style="background-image:url('imagenes/cayala.jpg');"></div>
      <div class="p-6">
        <h3 class="text-2xl font-bold text-[#8B0000]">Dinamia Cayalá</h3>
        <p><b>Dirección:</b> Boulevard Austriaco, Zona 16</p>
        <p><b>Teléfono:</b> +502 2219-3030</p>
      </div>
    </div>

  </div>

</section>


<footer class="bg-[#8B0000] text-white text-center py-5 mt-20">
  <p>© 2025 Hacienda Real Guatemala. Todos los derechos reservados.</p>
</footer>


<script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>

<script>
  var swiper = new Swiper(".mySwiper", {
    loop: true,
    autoplay: { delay: 3500, disableOnInteraction: false },
    pagination: { el: ".swiper-pagination", clickable: true },
    navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev" },
  });
</script>

</body>
</html>