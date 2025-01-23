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
        idMaquina IS NULL
        AND fechaInicio IS NULL 
        AND fechaFin IS NULL 
        AND idMaquinista IS NULL 
        AND estado = 'Pendiente';
";
$resultado = mysqli_query($conexion, $consulta);

// Comprobar si se ha enviado el formulario de máquina
if (isset($_POST['idTrabajo']) && isset($_POST['idMaquina'])) {
    $idTrabajo = $_POST['idTrabajo'];
    $idMaquina = $_POST['idMaquina'];

// Consultas
    // Consulta para asignar la máquina al trabajo (sin duplicar la máquina)
    $consulta = "
    UPDATE trabajo
    SET idMaquina = '$idMaquina'
    WHERE idTrabajo = '$idTrabajo' AND idMaquina IS NULL;
";
    $asignacionExitosa = mysqli_query($conexion, $consulta);
    
    // Si la asignación al trabajo fue exitosa
    if ($asignacionExitosa) {
        // Consulta para marcar la máquina como "Ocupada"
        $maquinaOcupada = "
        UPDATE maquinas
        SET estado = 'Ocupada'
        WHERE idMaquina = '$idMaquina' AND estado = 'Libre';
        ";
        
        // Ejecutar la consulta para cambiar el estado de la máquina
        $estadoOcupada = mysqli_query($conexion, $maquinaOcupada);
        
        // Si ambas consultas fueron exitosas
        if ($estadoOcupada) {
            header("Location: gestionarTrabajos.php");
            exit();
        } else {
            echo "<p>Error al marcar la máquina como ocupada: " . mysqli_error($conexion) . "</p>";
        }
    } else {
        echo "<p>Error al asignar la máquina al trabajo: " . mysqli_error($conexion) . "</p>";
    }
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
        <h1>Trabajos Pendientes</h1>
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
                <th>Asignar Máquina</th>
              </tr>";

            while ($fila = mysqli_fetch_assoc($resultado)) {
                echo "<tr>
                    <td>{$fila['idTrabajo']}</td>
                    <td>" . ($fila['idUsuario']) . "</td>
                    <td>{$fila['tipo']}</td>
                    <td>" . ($fila['fechaInicio']) . "</td>
                    <td>" . ($fila['fechaFin']) . "</td>
                    <td>" . ($fila['idMaquina']) . "</td>
                    <td>" . ($fila['idMaquinista']) . "</td>
                    <td>" . ($fila['idParcela']) . "</td>
                    <td>{$fila['estado']}</td>
                    <td>
                        <form action='gestionarTrabajos.php' method='POST'>
                            <input type='hidden' name='idTrabajo' value='{$fila['idTrabajo']}'>
                            <select name='idMaquina' required>
                                <option value=''>Seleccionar Máquina</option>";

                // Nueva consulta para obtener las máquinas disponibles para cada trabajo
                $resultado_maquinas = mysqli_query($conexion, "SELECT idMaquina, modelo FROM maquinas WHERE estado = 'Libre'");

                // Mostrar las opciones de máquinas disponibles
                while ($maquina = mysqli_fetch_assoc($resultado_maquinas)) {
                    echo "<option value='{$maquina['idMaquina']}'>{$maquina['modelo']}</option>";
                }

                echo "</select>
                            <button type='submit'>Asignar</button>
                        </form>
                    </td>
                </tr>";
            }

            echo "</table>";
        } else {
            echo "<p>No se encontraron trabajos con las condiciones especificadas.</p>";
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
