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

// Si no es admin, redirigir al login
if ($rol !== 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Menú - Admin</title>
    </head>
    <body>
        <h2>Bienvenido, <?php echo $user['nombre']; ?> (<?php echo $rol; ?>)</h2>

        <p>Correo electrónico: <?php echo $user['email']; ?></p>

        <h3>Panel de Administración</h3>
        <p>Bienvenido al panel de administración. Aquí puedes gestionar usuarios, roles, etc.</p>

        <p><a href="login.php">Cerrar sesión</a></p>
    </body>
</html>
