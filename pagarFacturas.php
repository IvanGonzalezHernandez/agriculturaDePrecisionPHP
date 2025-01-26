<?php
session_start();

// Incluir la conexión
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

// Verificar el rol del usuario
$rol = $user['rol'];

// Si no es agricultor, redirigir al login
if ($rol !== 'agricultor') {
    header("Location: login.php");
    exit();
}

// Obtener las facturas del agricultor (idUsuario es el agricultor)
$idAgricultor = $user['id'];
$consultaFacturas = "SELECT idFactura, idUsuario, idMaquinista, estado, PdfFactura FROM facturas WHERE idUsuario = $idAgricultor";
$resultFacturas = mysqli_query($conexion, $consultaFacturas);

// Procesar acción para aceptar una factura
if (isset($_POST['aceptar_factura'])) {
    $idFactura = $_POST['idFactura'];

    // Actualizar el estado de la factura a "Pagada"
    $actualizarFactura = "UPDATE facturas SET estado = 'Pagada' WHERE idFactura = $idFactura AND idUsuario = $idAgricultor";
    if (mysqli_query($conexion, $actualizarFactura)) {
        $mensaje = "Factura aceptada con éxito.";
    } else {
        $mensaje = "Error al aceptar la factura: " . mysqli_error($conexion);
    }

    // Refrescar la página para actualizar la lista
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Facturas - Agricultor</title>
        <link rel="stylesheet" href="./css/estilo.css">
    </head>
    <body>
        <?php
        include('./logo/logo.php');
        ?>
        <h2>Bienvenido, <?php echo $user['nombre']; ?> (<?php echo $rol; ?>)</h2>
        <h3>Tus Facturas</h3>

        <table border="1">
            <tr>
                <th>ID Factura</th>
                <th>ID Maquinista</th>
                <th>Estado</th>
                <th>Descargar PDF</th>
                <th>Acción</th>
            </tr>
            <?php
            // Mostrar las facturas
            if (mysqli_num_rows($resultFacturas) > 0) {
                while ($factura = mysqli_fetch_assoc($resultFacturas)) {
                    echo "<tr>";
                    echo "<td>" . $factura['idFactura'] . "</td>";
                    echo "<td>" . $factura['idMaquinista'] . "</td>";
                    echo "<td>" . $factura['estado'] . "</td>";

                    // Mostrar el enlace para descargar el PDF de la factura
                    echo "<td>";
                    if ($factura['PdfFactura']) {
                        echo "<a href='" . $factura['PdfFactura'] . "' target='_blank'><img src='./logo/logo_PDF.png' alt='PDF' width='30' height='30'></a>";
                    } else {
                        echo "Sin PDF";
                    }
                    echo "</td>";

                    echo "<td>";
                    if ($factura['estado'] === 'Pendiente') {
                        echo "<form method='POST' action=''>";
                        echo "<input type='hidden' name='idFactura' value='" . $factura['idFactura'] . "'>";
                        echo "<button type='submit' name='aceptar_factura'>Aceptar</button>";
                        echo "</form>";
                    } else {
                        echo "Pagada";
                    }
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No tienes facturas disponibles.</td></tr>";
            }
            ?>
        </table>

        <?php
        // Mostrar mensaje si hay algún error o confirmación
        if (isset($mensaje)) {
            echo "<p>$mensaje</p>";
        }
        ?>

        <!-- Botón de Atrás -->
        <form action="menuAgricultor.php">
            <button type="submit">Atrás</button>
        </form>
        <p><a href="login.php">Cerrar sesión</a></p>
    </body>
</html>
