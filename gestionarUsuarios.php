<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Incluir la conexión
include('./bd/conexion.php');

// Obtener la lista de usuarios
$usuariosConsulta = "SELECT u.id, u.nombre, u.email, r.nombre AS rol 
                     FROM usuarios u 
                     JOIN roles r ON u.rol_id = r.id";
$usuariosResultado = mysqli_query($conexion, $usuariosConsulta);

// Verificar si se ha enviado el formulario para eliminar un usuario
if (isset($_POST['eliminar'])) {
    $idUsuario = $_POST['eliminar']; // Obtener el id del usuario a eliminar
    // Consulta para eliminar el usuario
    $eliminarUsuario = "DELETE FROM usuarios WHERE id = $idUsuario";

    if (mysqli_query($conexion, $eliminarUsuario)) {
        echo "Usuario eliminado correctamente.";
        // Redirigir para que la tabla se recargue y se muestre el cambio
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Error al eliminar el usuario: " . mysqli_error($conexion);
    }
}

// Verificar si se ha enviado el formulario para añadir un nuevo usuario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['eliminar'])) {
    // Recuperar los datos del formulario
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Encriptar la contraseña
    $rol = $_POST['rol'];  // El valor de 'rol' ya es un id
    // Insertar datos en la tabla 'usuarios'
    $anadirUsuario = "INSERT INTO usuarios (nombre, email, password, rol_id) 
            VALUES ('$nombre', '$email', '$password', '$rol')";

    if (mysqli_query($conexion, $anadirUsuario)) {
        echo "Usuario añadido correctamente.";
        // Redirigir para que la tabla se recargue y se muestre el nuevo usuario
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Error al añadir el usuario: " . mysqli_error($conexion);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Añadir Usuario</title>
        <link rel="stylesheet" type="text/css" href="./css/estilo.css">
    </head>
    <body>
        <?php
        include('./logo/logo.php');
        ?>
        <h2>Tabla de Usuarios</h2>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Eliminar</th>
            </tr>
            <?php
            // Mostrar los usuarios en la tabla
            while ($usuario = mysqli_fetch_assoc($usuariosResultado)) {
                echo "<tr>";
                echo "<td>" . $usuario['id'] . "</td>";
                echo "<td>" . $usuario['nombre'] . "</td>";
                echo "<td>" . $usuario['email'] . "</td>";
                echo "<td>" . $usuario['rol'] . "</td>";
                echo "<td><form method='POST' action=''>
                      <button type='submit' name='eliminar' value='" . $usuario['id'] . "'>Eliminar</button>
                      </form></td>";
                echo "</tr>";
            }
            ?>
        </table>

        
        <form action="" method="POST">
            <h3>Añadir Nuevo Usuario:</h3>
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" required>

            <label for="email">Correo Electrónico</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" required>

            <label for="rol">Rol</label>
            <select id="rol" name="rol" required>
                <option value="1">Administrador</option>
                <option value="2">Agricultor</option>
                <option value="3">Maquinista</option>
            </select>

            <button type="submit">Añadir Usuario</button>
        </form>

        <!-- Botón de Atrás -->
        <form action="menuAdministrador.php">
            <button type="submit">Atrás</button>
        </form>
    </body>
</html>

<?php
// Cerrar conexión
mysqli_close($conexion);
?>
