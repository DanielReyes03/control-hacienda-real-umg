<?php
include("../db/conexion.php");

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $branch = $_POST['branch'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $guests = (int)$_POST['guests'];
    $comments = trim($_POST['comments']);

    // Validación básica
    if (empty($name) || empty($email) || empty($phone) || empty($branch) || empty($date) || empty($time) || $guests < 1) {
        $message = "Por favor, complete todos los campos obligatorios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Correo electrónico no válido.";
    } else {
        $conn = conectar();
        $stmt = $conn->prepare("INSERT INTO reservaciones (name, email, phone, branch, date, time, guests, comments) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssis", $name, $email, $phone, $branch, $date, $time, $guests, $comments);
        if ($stmt->execute()) {
            $message = "Reservación enviada exitosamente. ¡Gracias!";
        } else {
            $message = "Error al enviar la reservación. Intente nuevamente.";
        }
        $stmt->close();
        $conn->close();
    }
}

// Redirigir de vuelta al formulario con mensaje
header("Location: reservations.php?message=" . urlencode($message));
exit();
?>
