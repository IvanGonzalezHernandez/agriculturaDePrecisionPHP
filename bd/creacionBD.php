<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Crear Base de Datos</title>
    </head>
    <body>
        <?php
        // Incluir la conexión
        include('conexion.php');

        // Crear la base de datos
        $crearBD = "CREATE DATABASE IF NOT EXISTS AgriculturaDePrecision";
        if (mysqli_query($conexion, $crearBD)) {
            echo "Se ha creado la base de datos AgriculturaDePrecision correctamente.<br>";
        } else {
            die("Error al crear la base de datos AgriculturaDePrecision: " . mysqli_error($conexion));
        }

        // Crear las tablas
        // Tabla de roles
        $crearRoles = "CREATE TABLE IF NOT EXISTS roles (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(50) NOT NULL UNIQUE,
            descripcion TEXT NULL
        );";
        if (mysqli_query($conexion, $crearRoles)) {
            echo "Tabla 'roles' creada correctamente.<br>";
        } else {
            echo "Error al crear la tabla 'roles': " . mysqli_error($conexion) . "<br>";
        }

        // Insertar Roles si no existen
        $insertarRoles = "INSERT IGNORE INTO roles (nombre, descripcion) VALUES
            ('admin', 'Administrador del sistema'),
            ('agricultor', 'Agricultor'),
            ('maquinista', 'Maquinista');";

        // Ejecutar la consulta
        if (mysqli_query($conexion, $insertarRoles)) {
            echo "Roles insertados correctamente (si no existían).<br>";
        } else {
            echo "Error al insertar los roles: " . mysqli_error($conexion) . "<br>";
        }


        // Tabla de usuarios
        $crearUsuarios = "CREATE TABLE IF NOT EXISTS usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            rol_id INT NOT NULL,
            creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (rol_id) REFERENCES roles(id)
        );";
        if (mysqli_query($conexion, $crearUsuarios)) {
            echo "Tabla 'usuarios' creada correctamente.<br>";
        } else {
            echo "Error al crear la tabla 'usuarios': " . mysqli_error($conexion) . "<br>";
        }

        // Tabla de Agricultores (extiende Usuario)
        $crearAgricultores = "CREATE TABLE IF NOT EXISTS agricultores (
            idAgricultor INT AUTO_INCREMENT PRIMARY KEY,
            idUsuario INT NOT NULL,
            nombre VARCHAR(100) NOT NULL,
            password VARCHAR(255) NOT NULL,
            telefono INT NOT NULL,
            FOREIGN KEY (idUsuario) REFERENCES usuarios(id)
        );";
        if (mysqli_query($conexion, $crearAgricultores)) {
            echo "Tabla 'agricultores' creada correctamente.<br>";
        } else {
            echo "Error al crear la tabla 'agricultores': " . mysqli_error($conexion) . "<br>";
        }

        // Tabla de Maquinistas (extiende Usuario)
        $crearMaquinistas = "CREATE TABLE IF NOT EXISTS maquinistas (
            idMaquinista INT AUTO_INCREMENT PRIMARY KEY,
            idUsuario INT NOT NULL,
            certificacion ENUM('CERTIFICADO_A', 'CERTIFICADO_B', 'CERTIFICADO_C') NOT NULL,
            nombre VARCHAR(100) NOT NULL,
            FOREIGN KEY (idUsuario) REFERENCES usuarios(id)
        );";
        if (mysqli_query($conexion, $crearMaquinistas)) {
            echo "Tabla 'maquinistas' creada correctamente.<br>";
        } else {
            echo "Error al crear la tabla 'maquinistas': " . mysqli_error($conexion) . "<br>";
        }

        // Tabla de Maquinas
        $crearMaquinas = "CREATE TABLE IF NOT EXISTS maquinas (
            idMaquina INT AUTO_INCREMENT PRIMARY KEY,
            modelo VARCHAR(100) NOT NULL,
            capacidad INT NOT NULL,
            anho INT NOT NULL
        );";
        if (mysqli_query($conexion, $crearMaquinas)) {
            echo "Tabla 'maquinas' creada correctamente.<br>";
        } else {
            echo "Error al crear la tabla 'maquinas': " . mysqli_error($conexion) . "<br>";
        }

        // Tabla de Parcelas
        $crearParcelas = "CREATE TABLE IF NOT EXISTS parcelas (
            idParcela INT AUTO_INCREMENT PRIMARY KEY,
            idAgricultor INT NOT NULL,
            catastro VARCHAR(100) NOT NULL,
            superficie INT NOT NULL,
            FOREIGN KEY (idAgricultor) REFERENCES agricultores(idAgricultor)
        );";
        if (mysqli_query($conexion, $crearParcelas)) {
            echo "Tabla 'parcelas' creada correctamente.<br>";
        } else {
            echo "Error al crear la tabla 'parcelas': " . mysqli_error($conexion) . "<br>";
        }

        // Cerrar la conexión
        mysqli_close($conexion);
        ?>
    </body>
</html>
