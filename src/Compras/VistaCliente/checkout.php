<?php
session_start();
require_once(__DIR__ . '../../fpdf/fpdf.php');

// Verificar sesión activa
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../login/login.php");
    exit;
}

// Aquí deberías obtener los datos del pedido y del cliente desde la base de datos
// Para este ejemplo, usaremos datos simulados
$pedido_id = 7;
$cliente_nombre = "Carlos Barquero";
$cliente_direccion = "Ciudad de Guatemala";
$cliente_correo = "carlos@example.com";
$fecha = date("d/m/Y");
$total = 245.75;

$productos = [
    ["nombre" => "Pollo Campero Clásico", "cantidad" => 2, "precio" => 75.50],
    ["nombre" => "Camperitos (8 unidades)", "cantidad" => 1, "precio" => 45.75],
    ["nombre" => "Bebida Grande", "cantidad" => 2, "precio" => 24.25]
];

// Clase extendida para encabezado y pie de página
class PDF extends FPDF
{
    function Header()
    {
        // Logo (opcional)
        // $this->Image(__DIR__ . '/../../compras/images/logo.png', 10, 8, 25);
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, mb_convert_encoding("Restaurante Pollo Campero", 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
        $this->Ln(5);
        $this->SetFont('Arial', 'I', 11);
        $this->Cell(0, 10, mb_convert_encoding("Factura de compra", 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 9);
        $this->Cell(0, 10, 'Gracias por su compra - Pollo Campero', 0, 0, 'C');
    }
}

// Crear PDF
$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

// Datos del cliente
$pdf->Cell(0, 10, mb_convert_encoding("Factura No. $pedido_id", 'ISO-8859-1', 'UTF-8'), 0, 1);
$pdf->Cell(0, 10, mb_convert_encoding("Fecha: $fecha", 'ISO-8859-1', 'UTF-8'), 0, 1);
$pdf->Ln(5);
$pdf->Cell(0, 10, mb_convert_encoding("Cliente: $cliente_nombre", 'ISO-8859-1', 'UTF-8'), 0, 1);
$pdf->Cell(0, 10, mb_convert_encoding("Dirección: $cliente_direccion", 'ISO-8859-1', 'UTF-8'), 0, 1);
$pdf->Cell(0, 10, mb_convert_encoding("Correo: $cliente_correo", 'ISO-8859-1', 'UTF-8'), 0, 1);
$pdf->Ln(10);

// Tabla de productos
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(90, 10, 'Producto', 1, 0, 'C');
$pdf->Cell(30, 10, 'Cantidad', 1, 0, 'C');
$pdf->Cell(35, 10, 'Precio Unit.', 1, 0, 'C');
$pdf->Cell(35, 10, 'Subtotal', 1, 1, 'C');

$pdf->SetFont('Arial', '', 12);

foreach ($productos as $p) {
    $subtotal = $p['cantidad'] * $p['precio'];
    $pdf->Cell(90, 10, mb_convert_encoding($p['nombre'], 'ISO-8859-1', 'UTF-8'), 1);
    $pdf->Cell(30, 10, $p['cantidad'], 1, 0, 'C');
    $pdf->Cell(35, 10, number_format($p['precio'], 2), 1, 0, 'C');
    $pdf->Cell(35, 10, number_format($subtotal, 2), 1, 1, 'C');
}

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(155, 10, 'Total', 1, 0, 'R');
$pdf->Cell(35, 10, number_format($total, 2), 1, 1, 'C');

$pdf->Ln(15);
$pdf->SetFont('Arial', 'I', 11);
$pdf->MultiCell(0, 8, mb_convert_encoding("Nota: Conserve esta factura como comprobante de su compra. Gracias por preferirnos.", 'ISO-8859-1', 'UTF-8'));

// Guardar el PDF en la carpeta facturas
$ruta_factura = __DIR__ . '/../../facturas/factura_' . $pedido_id . '.pdf';
$pdf->Output('F', $ruta_factura);

// Mostrar alerta y redirigir
echo '
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura generada</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<script>
    Swal.fire({
        icon: "success",
        title: "¡Pedido confirmado!",
        text: "La factura ha sido generada exitosamente.",
        confirmButtonText: "Ver factura"
    }).then((result) => {
        if(result.isConfirmed){
            window.location.href = "../../facturas/factura_' . $pedido_id . '.pdf";
        }
    });
</script>
</body>
</html>
';
?>
