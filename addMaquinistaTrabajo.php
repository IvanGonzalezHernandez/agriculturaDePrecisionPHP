<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Incluir la conexión
include('./bd/conexion.php');

// Consulta para obtener los trabajos con las condiciones especificadas
$consulta = "
    SELECT 
        *
    FROM 
        trabajo
    WHERE
        idMaquinista IS NULL
        AND fechaInicio IS NULL 
        AND fechaFin IS NULL  
        AND estado = 'Pendiente';
";
$resultado = mysqli_query($conexion, $consulta);

// Comprobar si se ha enviado el formulario para asignar un maquinista
if (isset($_POST['idTrabajo']) && isset($_POST['idMaquinista'])) {
    $idTrabajo = $_POST['idTrabajo'];
    $idMaquinista = $_POST['idMaquinista'];
    
    // Consulta para asignar el maquinista al trabajo (evitando duplicación)
    $consulta = "
        UPDATE trabajo
        SET idMaquinista = '$idMaquinista'
        WHERE idTrabajo = '$idTrabajo' AND idMaquinista IS NULL;
    ";
    $asignacionExitosa = mysqli_query($conexion, $consulta);
    
    header("Location: addMaquinistaTrabajo.php");
    exit();

}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Trabajos Pendientes</title>
        <link rel="stylesheet" type="text/css" href="./css/style.css">
    </head>
    <body>
        <h1>Añadir Maquinista</h1>
        <?php
        if ($resultado && mysqli_num_rows($resultado) > 0) {
            // Mostrar los trabajos en una tabla
            echo "<table border='1'>";
            echo "<tr>
                <th>ID Trabajo</th>
                <th>ID Usuario</th>
                <th>Tipo</th>
                <th>Fecha de Inicio</th>
                <th>Fecha Fin</th>
                <th>ID Máquina</th>
                <th>ID Maquinista</th>
                <th>ID Parcela</th>
                <th>Estado</th>
                <th>Asignar Maquinista</th>
              </tr>";

            while ($fila = mysqli_fetch_assoc($resultado)) {
                echo "<tr>
                    <td>{$fila['idTrabajo']}</td>
                    <td>" . ($fila['idUsuario']) . "</td>
                    <td>{$fila['tipo']}</td>
                    <td>" . ($fila['fechaInicio'] ?? '') . "</td>
                    <td>" . ($fila['fechaFin'] ?? '') . "</td>
                    <td>" . ($fila['idMaquina'] ?? '') . "</td>
                    <td>" . ($fila['idMaquinista'] ?? '') . "</td>
                    <td>" . ($fila['idParcela'] ?? '') . "</td>
                    <td>{$fila['estado']}</td>
                    <td>
                        <form action='addMaquinistaTrabajo.php' method='POST'>
                            <input type='hidden' name='idTrabajo' value='{$fila['idTrabajo']}'>
                            <select name='idMaquinista' required>
                                <option value=''>Seleccionar Maquinista</option>";

                // Consulta para obtener los maquinistas disponibles
                $resultado_maquinistas = mysqli_query($conexion, "SELECT idUsuario, nombre FROM maquinistas WHERE estado = 'Libre'");

                // Mostrar las opciones de maquinistas disponibles
                while ($maquinista = mysqli_fetch_assoc($resultado_maquinistas)) {
                    echo "<option value='{$maquinista['idUsuario']}'>{$maquinista['nombre']}</option>";
                }

                echo "</select>
                            <button type='submit'>Asignar</button>
                        </form>
                    </td>
                </tr>";
            }

            echo "</table>";
        } else {
            echo "<p>No se encontraron trabajos en los que añadir maquinistas.</p>";
        }

        // Cerrar la conexión
        mysqli_close($conexion);
        ?>
        <!-- Botón de Atrás -->
        <form action="menuAdministrador.php">
            <button type="submit">Atrás</button>
        </form>
    </body>
</html>
