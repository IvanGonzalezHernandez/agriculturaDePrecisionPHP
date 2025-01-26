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

// Si no es maquinista, redirigir al login
if ($rol !== 'maquinista') {
    header("Location: login.php");
    exit();
}

// Conseguir el ID del maquinista
$consultaIdMaquinista = "SELECT id FROM usuarios WHERE email = '$email'";
$resultado = mysqli_query($conexion, $consultaIdMaquinista);
$idMaquinista = null;
if ($resultado && mysqli_num_rows($resultado) > 0) {
    $fila = mysqli_fetch_assoc($resultado);
    $idMaquinista = $fila['id'];
} else {
    echo "No se encontró el usuario con ese email.";
}

// Obtener los trabajos asignados al maquinista
$query_trabajos = "SELECT * FROM trabajo WHERE idMaquina IS NOT NULL AND idMaquinista = '$idMaquinista'";
$result_trabajos = mysqli_query($conexion, $query_trabajos);

// Procesar formulario para comenzar o finalizar trabajos
if (isset($_POST['idTrabajo'])) {
    $idTrabajo = $_POST['idTrabajo'];
    $fechaActual = date('Y-m-d H:i:s');

    if (isset($_POST['accion']) && $_POST['accion'] === 'empezar') {
        // Actualizar la columna fechaInicio
        $query = "UPDATE trabajo SET fechaInicio = '$fechaActual', estado = 'En progreso' WHERE idTrabajo = '$idTrabajo'";
        $resultado = mysqli_query($conexion, $query);

        if (!$resultado) {
            echo "Error al actualizar fechaInicio: " . mysqli_error($conexion);
        }
    } elseif (isset($_POST['accion']) && $_POST['accion'] === 'finalizar') {
        // Actualizar la columna fechaFin
        $query = "UPDATE trabajo SET fechaFin = '$fechaActual', estado = 'Completado' WHERE idTrabajo = '$idTrabajo'";
        $resultado = mysqli_query($conexion, $query);

        if (!$resultado) {
            echo "Error al actualizar fechaFin: " . mysqli_error($conexion);
        }
    }

    // Redirigir de vuelta al panel
    header("Location: menuMaquinista.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Dashboard - Maquinista</title>
        <link rel="stylesheet" type="text/css" href="./css/estilo.css">
    </head>
    <body>
        <?php
        include('./logo/logo.php');
        ?>
        <h2>Bienvenido, <?php echo $user['nombre']; ?> (<?php echo $rol; ?>)</h2>

        <p>Correo electrónico: <?php echo $user['email']; ?></p>

        <h3>Panel de Maquinista</h3>
        <p>Bienvenido maquinista. Aquí puedes gestionar tus máquinas y trabajos asignados.</p>

        <h4>Trabajos Asignados</h4>
        <table border="1">
            <thead>
                <tr>
                    <th>ID Trabajo</th>
                    <th>Tipo</th>
                    <th>Fecha inicio</th>
                    <th>Fecha fin</th>
                    <th>ID Maquina</th>
                    <th>ID Parcela</th>
                    <th>Estado trabajo</th>
                    <th>Empezar</th>
                    <th>Finalizar</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($trabajo = mysqli_fetch_assoc($result_trabajos)) { ?>
                    <tr>
                        <td><?php echo $trabajo['idTrabajo']; ?></td>
                        <td><?php echo $trabajo['tipo']; ?></td>
                        <td><?php echo $trabajo['fechaInicio']; ?></td>
                        <td><?php echo $trabajo['fechaFin']; ?></td>
                        <td><?php echo $trabajo['idMaquina']; ?></td>
                        <td><?php echo $trabajo['idParcela']; ?></td>
                        <td><?php echo $trabajo['estado']; ?></td>
                        <td>
                            <!-- Botón para empezar trabajo -->
                            <form method="POST" action="menuMaquinista.php">
                                <input type="hidden" name="idTrabajo" value="<?php echo $trabajo['idTrabajo']; ?>">
                                <input type="hidden" name="accion" value="empezar">
                                <button type="submit">Empezar Trabajo</button>
                            </form>
                        </td>
                        <td>
                            <!-- Botón para finalizar trabajo -->
                            <form method="POST" action="menuMaquinista.php">
                                <input type="hidden" name="idTrabajo" value="<?php echo $trabajo['idTrabajo']; ?>">
                                <input type="hidden" name="accion" value="finalizar">
                                <button type="submit">Finalizar Trabajo</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <p><a href="login.php">Cerrar sesión</a></p>
    </body>
</html>

