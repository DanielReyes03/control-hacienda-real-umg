<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservaciones - Hacienda Real Guatemala</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <h1>Hacienda Real - Reservaciones</h1>
            <p>Reserva tu mesa en el steakhouse perfecto</p>
        </div>
    </header>

    <section class="reservation-form">
        <div class="container">
            <h2>Formulario de Reservación</h2>
            <div class="form-container">
                <form action="#" method="post">
                    <div class="form-group">
                        <label for="name">Nombre Completo</label>
                        <input type="text" id="name" name="name" required placeholder="Ej. Juan Pérez">
                    </div>
                    <div class="form-group">
                        <label for="email">Correo Electrónico</label>
                        <input type="email" id="email" name="email" required placeholder="Ej. juan@ejemplo.com">
                    </div>
                    <div class="form-group">
                        <label for="phone">Teléfono</label>
                        <input type="tel" id="phone" name="phone" required placeholder="Ej. +502 1234 5678">
                    </div>
                    <div class="form-group">
                        <label for="branch">Sucursal</label>
                        <select id="branch" name="branch" required>
                            <option value="" disabled selected>Selecciona una sucursal</option>
                            <option value="zona10">Zona 10 (Sede Principal)</option>
                            <option value="zona11">Zona 11 (Las Majadas)</option>
                            <option value="zona14">Zona 14</option>
                            <option value="condado">Condado Concepción</option>
                            <option value="cayala">Dinamia Cayalá</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="date">Fecha</label>
                        <input type="date" id="date" name="date" required>
                    </div>
                    <div class="form-group">
                        <label for="time">Hora</label>
                        <input type="time" id="time" name="time" required>
                    </div>
                    <div class="form-group">
                        <label for="guests">Número de Personas</label>
                        <input type="number" id="guests" name="guests" min="1" max="100" required placeholder="Ej. 4">
                    </div>
                    <div class="form-group">
                        <label for="comments">Comentarios Adicionales</label>
                        <textarea id="comments" name="comments" placeholder="Ej. Mesa en terraza, evento especial"></textarea>
                    </div>
                    <button type="submit" class="btn">Enviar Reservación</button>
                </form>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 Hacienda Real Guatemala. Todos los derechos reservados.</p>
            <p>Contáctanos al +502 2380-8383 para más información.</p>
        </div>
    </footer>
</body>
</html>