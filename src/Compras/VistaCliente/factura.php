<?php
require('../../fpdf/fpdf.php');
include("../../db/conexion.php");
$conn = conectar();

if (!isset($_GET['venta_id'])) {
    die("No se especificó la venta.");
}
$venta_id = intval($_GET['venta_id']);

// 🔹 Obtener datos de la venta y cliente
$venta = $conn->query("
    SELECT v.*, c.nombre AS cliente, c.telefono, c.direccion
    FROM ventas v
    JOIN clientes c ON c.id = v.cliente_id
    WHERE v.id = $venta_id
")->fetch_assoc();

// 🔹 Obtener detalles del pedido
$detalle = $conn->query("
    SELECT p.nombre, d.cantidad, d.precio_unitario, d.precio_total
    FROM ventas_detalle d
    JOIN productos p ON p.id = d.producto_id
    WHERE d.venta_id = $venta_id
");

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'Factura - La Hacienda Real', 0, 1, 'C');
        $this->Ln(5);
    }
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Gracias por su compra - La Hacienda Real', 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

// Datos del cliente
$pdf->Cell(0, 10, utf8_decode("Cliente: {$venta['cliente']}"), 0, 1);
$pdf->Cell(0, 10, utf8_decode("Teléfono: {$venta['telefono']}"), 0, 1);
$pdf->Cell(0, 10, utf8_decode("Dirección: {$venta['direccion']}"), 0, 1);
$pdf->Ln(5);
$pdf->Cell(0, 10, utf8_decode("Fecha: {$venta['fecha_venta']}"), 0, 1);
$pdf->Cell(0, 10, utf8_decode("Tipo de Orden: {$venta['tipo_orden']}"), 0, 1);
$pdf->Ln(10);

// Tabla de productos
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(80, 10, 'Producto', 1);
$pdf->Cell(30, 10, 'Cant.', 1, 0, 'C');
$pdf->Cell(40, 10, 'Precio Unit.', 1, 0, 'C');
$pdf->Cell(40, 10, 'Subtotal', 1, 1, 'C');
$pdf->SetFont('Arial', '', 12);

$total = 0;
while ($row = $detalle->fetch_assoc()) {
    $pdf->Cell(80, 10, utf8_decode($row['nombre']), 1);
    $pdf->Cell(30, 10, $row['cantidad'], 1, 0, 'C');
    $pdf->Cell(40, 10, "Q" . number_format($row['precio_unitario'], 2), 1, 0, 'C');
    $pdf->Cell(40, 10, "Q" . number_format($row['precio_total'], 2), 1, 1, 'C');
    $total += $row['precio_total'];
}

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(150, 10, 'TOTAL', 1, 0, 'R');
$pdf->Cell(40, 10, "Q" . number_format($total, 2), 1, 1, 'C');

$pdf->Ln(15);
$pdf->SetFont('Arial', 'I', 11);
$pdf->MultiCell(0, 10, utf8_decode("¡Gracias por su compra! Esperamos verlo pronto en La Hacienda Real."));

$pdf->Output();
?>
