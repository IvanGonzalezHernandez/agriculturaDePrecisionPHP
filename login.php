<?php
session_start();
session_destroy();
session_start();

// Incluir la conexión
include('./bd/conexion.php');

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Consultar la base de datos para verificar las credenciales
    $consulta = "SELECT u.*, r.nombre AS rol FROM usuarios u JOIN roles r ON u.rol_id = r.id WHERE u.email = '$email'";
    $result = mysqli_query($conexion, $consulta);

    // Verificar si se encontró un usuario con ese correo
    if (mysqli_num_rows($result) == 1) {
        // Obtener los datos del usuario
        $user = mysqli_fetch_assoc($result);

        // Verificar si la contraseña ingresada coincide con la almacenada (encriptada)
        if (password_verify($password, $user['password'])) {
            // Iniciar sesión y almacenar los datos en la sesión
            $_SESSION['email'] = $email;
            $_SESSION['rol'] = $user['rol'];

            // Redirigir al panel correspondiente según el rol
            if ($user['rol'] == 'admin') {
                header("Location: menuAdministrador.php");
            } elseif ($user['rol'] == 'agricultor') {
                header("Location: menuAgricultor.php");
            } elseif ($user['rol'] == 'maquinista') {
                header("Location: menuMaquinista.php");
            } else {
                // En caso de que el rol no sea válido
                header("Location: login.php");
            }
            exit();
        } else {
            $error = "Credenciales incorrectas.";
        }
    } else {
        $error = "Credenciales incorrectas.";
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Login</title>
        <link rel="stylesheet" href="./css/logo.css">
    </head>
    <body>


        <div class="login-container">
            <form method="POST" action="login.php" class="login-form">
                <img src="./logo/agrarium_logo.png" alt="Logo agrarium" class="logo"/>
                <h2>Iniciar sesión</h2>
                <!-- Mostrar error si las credenciales son incorrectas -->
                <?php
                if (isset($error)) {
                    echo "<p style='color:red;'>$error</p>";
                }
                ?>
                <label for="email">Email:</label>
                <input type="email" name="email" required>

                <label for="password">Contraseña:</label>
                <input type="password" name="password" required>

                <button type="submit">Iniciar sesión</button>
            </form>
        </div>
    </body>
</html>

