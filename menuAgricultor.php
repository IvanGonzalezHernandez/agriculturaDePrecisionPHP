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


// Obtener las parcelas del agricultor
$idAgricultor = $user['id'];
$consultaParcelas = "SELECT * FROM parcelas WHERE idAgricultor = $idAgricultor";
$resultParcelas = mysqli_query($conexion, $consultaParcelas);

// Procesar formulario de añadir parcela
if (isset($_POST['add_parcela'])) {
    $catastro = $_POST['catastro'];
    $superficie = $_POST['superficie'];
    $idAgricultor = $user['id'];

    // Insertar la nueva parcela en la base de datos
    $insertarParcela = "INSERT INTO parcelas (catastro, superficie, idAgricultor) VALUES ('$catastro', '$superficie', '$idAgricultor')";
    if (mysqli_query($conexion, $insertarParcela)) {
        // Redireccionar para refrescar la página
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $mensaje = "Error al añadir la parcela: " . mysqli_error($conexion);
    }
}




if (isset($_POST['eliminar_parcela'])) {
    $idParcela = $_POST['idParcela'];

    // Consulta para eliminar la parcela
    $eliminarParcela = "DELETE FROM parcelas WHERE idParcela = $idParcela";
    if (mysqli_query($conexion, $eliminarParcela)) {
        $mensaje = "Parcela eliminada con éxito.";
    } else {
        $mensaje = "Error al eliminar la parcela: " . mysqli_error($conexion);
    }

    // Refrescar la página para actualizar la lista
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}




// Procesar formulario de añadir trabajo
if (isset($_POST['add_trabajo'])) {
    $idParcela = $_POST['idParcela'];
    $tipo = $_POST['tipo'];

    // Validar que la parcela pertenece al agricultor
    $validarParcela = "SELECT idParcela FROM parcelas WHERE idParcela = $idParcela AND idAgricultor = $idAgricultor";
    $resultValidar = mysqli_query($conexion, $validarParcela);

    if (mysqli_num_rows($resultValidar) > 0) {
        // Insertar el nuevo trabajo en la base de datos
        $insertarTrabajo = "INSERT IGNORE INTO trabajo (idParcela, idUsuario, tipo) VALUES ('$idParcela', '$idAgricultor', '$tipo')";
        if (mysqli_query($conexion, $insertarTrabajo)) {
            $mensaje = "Trabajo añadido con éxito.";
        } else {
            $mensaje = "Error al añadir el trabajo: " . mysqli_error($conexion);
        }
    } else {
        $mensaje = "Parcela no válida o no pertenece a este agricultor.";
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Dashboard - Agricultor</title>
        <link rel="stylesheet" type="text/css" href="./css/estilo.css">
    </head>
    <body>


        <?php
        include('./logo/logo.php');
        ?>
        <h2>Bienvenido, <?php echo $user['nombre']; ?> (<?php echo $rol; ?>)</h2>

        <p>Correo electrónico: <?php echo $user['email']; ?></p>

        <h3>Panel de Agricultor</h3>
        <p>Bienvenido agricultor. Aquí puedes gestionar tus parcelas, ver estadísticas, etc.</p>

        <h4>Tus Parcelas</h4>

        <table border="1">
            <tr>
                <th>Id</th>
                <th>Catastro</th>
                <th>Superficie</th>
                <th>Eliminar</th>
            </tr>
            <?php
            // Mostrar las parcelas
            if (mysqli_num_rows($resultParcelas) > 0) {
                while ($parcela = mysqli_fetch_assoc($resultParcelas)) {
                    echo "<tr>";
                    echo "<td>" . $parcela['idParcela'] . "</td>";
                    echo "<td>" . $parcela['catastro'] . "</td>";
                    echo "<td>" . $parcela['superficie'] . " m²</td>";
                    echo "<td>";
                    echo "<form method='POST' action=''>";
                    echo "<input type='hidden' name='idParcela' value='" . $parcela['idParcela'] . "'>";
                    echo "<button type='submit' name='eliminar_parcela'>Eliminar</button>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }
            }
            ?>
        </table>

        
        <form method="POST" action="">
            <h3>Añadir una nueva parcela:</h3>
            <label for="catastro">Catastro:</label>
            <input type="text" id="catastro" name="catastro" required>
            <br>
            <label for="superficie">Superficie (en m²):</label>
            <input type="number" id="superficie" name="superficie" required>
            <br>
            <button type="submit" name="add_parcela">Añadir Parcela</button>
        </form>


        
        <form method="POST" action="">
            <h3>Solicitar trabajo en parcela:</h3>
            <label for="parcela">Seleccionar parcela:</label>
            <select id="parcela" name="idParcela" required>
                <?php
                // Rellenar las opciones del selector de parcelas
                mysqli_data_seek($resultParcelas, 0); // Reiniciar el puntero del resultado
                if (mysqli_num_rows($resultParcelas) > 0) {
                    while ($parcela = mysqli_fetch_assoc($resultParcelas)) {
                        echo "<option value='" . $parcela['idParcela'] . "'>" . $parcela['catastro'] . "</option>";
                    }
                }
                ?>
            </select>
            <br>
            <label for="tipo">Seleccionar el tipo de trabajo:</label>
            <select id="tipo" name="tipo" required>
                <option value="Arado">Arado</option>
                <option value="Siembra">Siembra</option>
                <option value="Cosecha">Cosecha</option>
                <option value="Riego">Riego</option>
            </select>
            <br>
            <button type="submit" name="add_trabajo">Añadir Trabajo</button>
        </form>

        <?php
        if (isset($mensaje)) {
            echo "<p>$mensaje</p>";
        }
        ?>
        
        
        <form action='./referenciaCatastral/catastro/datosCatastrales.php'>
            <h3>Consulta estadisticas de tus parcelas:</h3>
            <button>Datos Catastrales</button></form>
        
        <form action='pagarFacturas.php'>
            <h3>Consulta y paga tus  facturas:</h3>
            <button>Pagar Facturas</button></form>

        <p><a href="login.php">Cerrar sesión</a></p>




    </body>
</html>