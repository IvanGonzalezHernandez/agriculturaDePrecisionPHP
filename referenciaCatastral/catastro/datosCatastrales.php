<?php
session_start();

// Incluir la conexión
include('../../bd/conexion.php');

// Verificar si el usuario está autenticado
if (!isset($_SESSION['email'])) {
    header("Location: ../../login.php");
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
    header("Location: ../../login.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Buscar Parcela</title>
    </head>
    <body>
        <h1>Buscar Parcela</h1>
        <label for="referencia">Introduce la referencia de la parcela:</label>
        <input type="text" id="referencia" placeholder="Ejemplo: 123456789012345">
        <button id="buscar">Buscar</button>
        <div id="resultado"></div>
        <script src="script.js"></script>


        123456789012345
        <br>
        987654321098765
        <br>
        111223344556677
        <br>
        223344556677889

        <!-- Botón de Atrás -->
        <form action="../../menuAgricultor.php">
            <button type="submit">Atrás</button>
        </form>
    </body>
</html>