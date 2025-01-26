<?php
session_start();

// Incluir la librería FPDF
require('fpdf186/fpdf.php');

// Incluir la conexión a la base de datos
include('./bd/conexion.php');

// Verificar si el usuario está autenticado
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Obtener el usuario logueado
$email = $_SESSION['email'];
$query = "SELECT u.*, r.nombre AS rol FROM usuarios u JOIN roles r ON u.rol_id = r.id WHERE u.email = '$email'";
$result = mysqli_query($conexion, $query);
$user = mysqli_fetch_assoc($result);

// Verificar el rol del usuario (solo admin puede ver esta página)
$rol = $user['rol'];
if ($rol !== 'admin') {
    header("Location: login.php");
    exit();
}

// Obtener los trabajos completados (sin JOIN)
$query_trabajos_completados = "SELECT * FROM trabajo WHERE estado = 'Completado'";
$result_trabajos_completados = mysqli_query($conexion, $query_trabajos_completados);

// Verificar si se recibió el ID del trabajo desde el formulario
if (isset($_POST['idTrabajo'])) {
    $idTrabajo = $_POST['idTrabajo'];
    $idUsuario = $_POST['idUsuario'];

    // Consultar los datos del trabajo
    $query_trabajo = "SELECT * FROM trabajo WHERE idTrabajo = '$idTrabajo'";
    $result_trabajo = mysqli_query($conexion, $query_trabajo);

    if ($result_trabajo && mysqli_num_rows($result_trabajo) > 0) {
        $trabajo = mysqli_fetch_assoc($result_trabajo);

        // Calcular el monto de la factura (Ejemplo: cálculo basado en la duración del trabajo)
        function calcularCantidad($trabajo) {
            $fechaInicio = strtotime($trabajo['fechaInicio']);
            $fechaFin = strtotime($trabajo['fechaFin']);
            $duracionHoras = ($fechaFin - $fechaInicio) / 3600; // Convertir segundos a horas
            $tarifaPorHora = 50; // Tarifa fija por hora (ajustar según sea necesario)
            return round($duracionHoras * $tarifaPorHora, 2); // Redondear a 2 decimales
        }

        // Calcular el monto
        $monto = calcularCantidad($trabajo);
        $fechaFactura = date("Y-m-d H:i:s"); // Fecha actual
        // Crear el objeto PDF
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);

        // Agregar el logo (ajusta la ruta y el tamaño según sea necesario)
        $logo = './logo/agrarium_logo.png';
        $pdf->Image($logo, 94, 8, 33);

        // Título
        $pdf->Cell(200, 10, 'Factura de Trabajo', 0, 1, 'C');

        // Datos del trabajo
        $pdf->Ln(10); // Salto de línea
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(100, 10, 'ID Trabajo: ' . $trabajo['idTrabajo'], 0, 1);
        $pdf->Cell(100, 10, 'Maquinista: ' . $trabajo['idMaquinista'], 0, 1);
        $pdf->Cell(100, 10, 'Tipo de Trabajo: ' . $trabajo['tipo'], 0, 1);
        $pdf->Cell(100, 10, 'Fecha Inicio: ' . $trabajo['fechaInicio'], 0, 1);
        $pdf->Cell(100, 10, 'Fecha Fin: ' . $trabajo['fechaFin'], 0, 1);
        $pdf->Cell(100, 10, 'ID Maquina: ' . $trabajo['idMaquina'], 0, 1);
        $pdf->Cell(100, 10, 'ID Parcela: ' . $trabajo['idParcela'], 0, 1);
        $pdf->Cell(100, 10, 'Estado: ' . $trabajo['estado'], 0, 1);
        $pdf->Ln(10); // Salto de línea
        // Monto de la factura
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(100, 10, 'Cantidad Total: ' . $monto . ' EUR', 0, 1);

        // Fecha de emisión
        $pdf->Cell(100, 10, 'Fecha de Emision: ' . $fechaFactura, 0, 1);

        // Guardar el archivo PDF en el servidor
        $pdfOutput = 'facturas/Factura_Trabajo_' . $idTrabajo . '.pdf';
        $pdf->Output('F', $pdfOutput); // Guardar como archivo
        // Insertar los datos en la base de datos

        $idMaquinista = $trabajo['idMaquinista']; // Obtener el id del maquinista desde los datos del trabajo

        $query_insert_factura = "INSERT INTO facturas (idUsuario, idMaquinista, PdfFactura, estado) 
                                 VALUES ('$idUsuario', '$idMaquinista', '$pdfOutput', 'Pendiente')";

        if (mysqli_query($conexion, $query_insert_factura)) {
            echo "Factura generada y guardada correctamente. <a href='$pdfOutput' target='_blank'>Ver factura</a>";
        } else {
            echo "Error al guardar la factura: " . mysqli_error($conexion);
        }
        exit();
    } else {
        echo "No se ha encontrado el trabajo con ID $idTrabajo.";
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Trabajos Completados</title>
        <link rel="stylesheet" type="text/css" href="./css/estilo.css">
    </head>
    <body>
<?php
include('./logo/logo.php');
?>
        <h2>Panel de Administración - Trabajos Completados</h2>
        <p>Bienvenido, <?php echo $user['nombre']; ?> (<?php echo $rol; ?>)</p>

        <h3>Lista de Trabajos Finalizados</h3>
        <table border="1">
            <thead>
                <tr>
                    <th>ID Trabajo</th>
                    <th>Usuario</th>
                    <th>Maquinista</th>
                    <th>Tipo</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>ID Maquina</th>
                    <th>ID Parcela</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
<?php while ($trabajo = mysqli_fetch_assoc($result_trabajos_completados)) { ?>
                    <tr>
                        <td><?php echo $trabajo['idTrabajo']; ?></td>
                        <td><?php echo $trabajo['idUsuario']; ?></td>
                        <td><?php echo $trabajo['idMaquinista']; ?></td>
                        <td><?php echo $trabajo['tipo']; ?></td>
                        <td><?php echo $trabajo['fechaInicio']; ?></td>
                        <td><?php echo $trabajo['fechaFin']; ?></td>
                        <td><?php echo $trabajo['idMaquina']; ?></td>
                        <td><?php echo $trabajo['idParcela']; ?></td>
                        <td><?php echo $trabajo['estado']; ?></td>
                        <td>
                            <form method="POST" action="gestionarFacturas.php">
                                <input type="hidden" name="idTrabajo" value="<?php echo $trabajo['idTrabajo']; ?>">
                                <input type="hidden" name="idUsuario" value="<?php echo $trabajo['idUsuario']; ?>">
                                <button type="submit">Emitir Factura</button>
                            </form>
                        </td>
                    </tr>
<?php } ?>
            </tbody>
        </table>

        <!-- Botón de Atrás -->
        <form action="menuAdministrador.php">
            <button type="submit">Atrás</button>
        </form>
    </body>
</html>
