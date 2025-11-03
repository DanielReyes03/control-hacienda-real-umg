<?php
header('Content-Type: application/json');
$pdo = new PDO("mysql:host=db;dbname=mydb;charset=utf8mb4", "user", "userpassword");

// Obtén el mes actual
$mes = date('n');
$anio = date('Y');

// Total de ventas del mes
$sqlTotal = "SELECT SUM(total) AS total_mes FROM ventas 
             WHERE estado='cerrada' 
             AND MONTH(fecha_venta)=? 
             AND YEAR(fecha_venta)=?";
$stmt = $pdo->prepare($sqlTotal);
$stmt->execute([$mes, $anio]);
$total_mes = (float)$stmt->fetchColumn();

// Ticket promedio (promedio de total por venta cerrada del mes)
$sqlTicket = "SELECT AVG(total) AS ticket_promedio FROM ventas 
              WHERE estado='cerrada' 
              AND MONTH(fecha_venta)=? 
              AND YEAR(fecha_venta)=?";
$stmt = $pdo->prepare($sqlTicket);
$stmt->execute([$mes, $anio]);
$ticket_promedio = (float)$stmt->fetchColumn();

// Respuesta JSON
echo json_encode([
  'total_mes' => round($total_mes, 2),
  'ticket_promedio' => round($ticket_promedio, 2)
]);
?>
