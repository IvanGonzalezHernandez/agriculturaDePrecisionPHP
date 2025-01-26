<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Crear Base de Datos</title>
    </head>
    <body>
        <?php
        // Conectar con el servidor de base de datos
        $conexion = mysqli_connect("localhost", "root", "");

        // Crear la base de datos
        $crearBD = "CREATE DATABASE IF NOT EXISTS agriculturaDePrecision";
        if (mysqli_query($conexion, $crearBD)) {
            echo "Se ha creado la base de datos agriculturadeprecision correctamente.<br>";
        } else {
            die("Error al crear la base de datos agriculturadeprecision: " . mysqli_error($conexion));
        }

        // Seleccionar base de datos
        mysqli_select_db($conexion, "agriculturaDePrecision")
                or die("No se puede seleccionar la base de datos");

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
            idUsuario INT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(100) NOT NULL,
            FOREIGN KEY (idUsuario) REFERENCES usuarios(id)
        );";
        if (mysqli_query($conexion, $crearAgricultores)) {
            echo "Tabla 'agricultores' creada correctamente.<br>";
        } else {
            echo "Error al crear la tabla 'agricultores': " . mysqli_error($conexion) . "<br>";
        }

        // Tabla de Maquinistas (extiende Usuario)
        $crearMaquinistas = "CREATE TABLE IF NOT EXISTS maquinistas (
            idUsuario INT PRIMARY KEY,
            certificacion ENUM('CERTIFICADO_A', 'CERTIFICADO_B', 'CERTIFICADO_C'),
            nombre VARCHAR(100) NOT NULL,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(100) NOT NULL,
            estado ENUM('Libre', 'Ocupado') DEFAULT 'Libre', -- Estado del trabajador
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
            tipo ENUM('Arado', 'Siembra','Cosecha', 'Riego'), -- Breve descripción del trabajo
            capacidad INT NOT NULL,
            anho INT NOT NULL,
            estado ENUM('Libre', 'Ocupada', 'Reparando') DEFAULT 'Libre' -- Estado del trabajo
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
        FOREIGN KEY (idAgricultor) REFERENCES agricultores(idUsuario) ON DELETE CASCADE ON UPDATE CASCADE
        );";
        if (mysqli_query($conexion, $crearParcelas)) {
            echo "Tabla 'parcelas' creada correctamente.<br>";
        } else {
            echo "Error al crear la tabla 'parcelas': " . mysqli_error($conexion) . "<br>";
        }

        $crearTablaTrabajos = "
        CREATE TABLE IF NOT EXISTS trabajo (
        idTrabajo INT AUTO_INCREMENT PRIMARY KEY, -- Identificador único para cada trabajo
        idUsuario INT NOT NULL,
        tipo ENUM('Arado', 'Siembra','Cosecha', 'Riego'), -- Breve descripción del trabajo
        fechaInicio DATE, -- Fecha de inicio del trabajo
        fechaFin DATE, -- Fecha de finalización (puede ser NULL si no ha terminado)
        idMaquina INT, -- Relación con la tabla 'maquinas'
        idMaquinista INT, -- Relación con la tabla 'maquinistas'
        idParcela INT, -- Relación con la tabla 'parcelas'
        estado ENUM('Pendiente', 'En progreso', 'Completado') DEFAULT 'Pendiente', -- Estado del trabajo
        FOREIGN KEY (idMaquina) REFERENCES maquinas(idMaquina) ON DELETE SET NULL,
        FOREIGN KEY (idMaquinista) REFERENCES maquinistas(idUsuario) ON DELETE SET NULL,
        FOREIGN KEY (idParcela) REFERENCES parcelas(idParcela) ON DELETE SET NULL -- Relación con la tabla 'parcelas'
        );
        ";

        // Ejecutar la consulta para crear la tabla
        if (mysqli_query($conexion, $crearTablaTrabajos)) {
            echo "Tabla 'trabajos' creada correctamente.<br>";
        } else {
            echo "Error al crear la tabla 'trabajos': " . mysqli_error($conexion) . "<br>";
        }

        // Crear la tabla de facturas
        $crearFacturas = "
        CREATE TABLE IF NOT EXISTS facturas (
        idFactura INT AUTO_INCREMENT PRIMARY KEY,
        idUsuario INT NOT NULL,
        idMaquinista INT NOT NULL,
        PdfFactura VARCHAR(255) NOT NULL,
        estado ENUM('Pendiente', 'Pagada') DEFAULT 'Pendiente',
        FOREIGN KEY (idUsuario) REFERENCES usuarios(id) ON DELETE CASCADE ON UPDATE CASCADE,
        FOREIGN KEY (idMaquinista) REFERENCES maquinistas(idUsuario) ON DELETE CASCADE ON UPDATE CASCADE
        );
        ";

        if (mysqli_query($conexion, $crearFacturas)) {
            echo "Tabla 'facturas' creada correctamente.<br>";
        } else {
            echo "Error al crear la tabla 'facturas': " . mysqli_error($conexion) . "<br>";
        }


        //Triggers
        $crearTriggerAgricultores = "
        CREATE TRIGGER insertar_en_agricultores
        AFTER INSERT ON usuarios
        FOR EACH ROW
        BEGIN
            IF NEW.rol_id = 2 THEN
                INSERT INTO agricultores (idUsuario, nombre, password, email)
                VALUES (NEW.id, NEW.nombre, NEW.password, NEW.email);
            END IF;
        END;
        ";

        if (mysqli_query($conexion, $crearTriggerAgricultores)) {
            echo "Trigger 'insertar_en_agricultores' creado correctamente.<br>";
        } else {
            echo "Error al crear el trigger 'insertar_en_agricultores': " . mysqli_error($conexion) . "<br>";
        }

        $crearTriggerMaquinistas = "
        CREATE TRIGGER insertar_en_maquinistas
        AFTER INSERT ON usuarios
        FOR EACH ROW
        BEGIN
            IF NEW.rol_id = 3 THEN
                INSERT INTO maquinistas (idUsuario, nombre, password, email)
                VALUES (NEW.id, NEW.nombre, NEW.password, NEW.email);
        END IF;
        END;
        ";

        if (mysqli_query($conexion, $crearTriggerMaquinistas)) {
            echo "Trigger 'insertar_en_maquinistas' creado correctamente.<br>";
        } else {
            echo "Error al crear el trigger 'insertar_en_maquinistas': " . mysqli_error($conexion) . "<br>";
        }

        // Insertar usuarios en la tabla
        $insertarUsuarios = "
        INSERT IGNORE INTO usuarios (nombre, email, password, rol_id) VALUES
        ('Administrador', 'admin@gmail.com', '" . password_hash('1234', PASSWORD_DEFAULT) . "', 1),
        ('Agricultor', 'agricultor@gmail.com', '" . password_hash('1234', PASSWORD_DEFAULT) . "', 2),
        ('Maquinista', 'maquinista@gmail.com', '" . password_hash('1234', PASSWORD_DEFAULT) . "', 3);
        ";
        if (mysqli_query($conexion, $insertarUsuarios)) {
            echo "Usuarios insertados correctamente.<br>";
        } else {
            echo "Error al insertar usuarios: " . mysqli_error($conexion) . "<br>";
        }



        // Consulta para insertar datos en la tabla maquinas
        $insertarDatosMaquinas = "
        INSERT INTO maquinas (modelo,tipo, capacidad, anho) VALUES
        ('Excavadora XZ100','Arado', 5000, 2020),
        ('Retroexcavadora RX200','Siembra', 3000, 2018),
        ('Grúa Industrial GI500','Riego', 15000, 2021),
        ('Compactadora CP300','Cosecha', 2000, 2019),
        ('Cargadora CL400','Arado', 4000, 2022);
        ";

        // Ejecutar la consulta
        if (mysqli_query($conexion, $insertarDatosMaquinas)) {
            echo "Datos insertados correctamente.<br>";
        } else {
            echo "Error al insertar datos en la tabla 'maquinas': " . mysqli_error($conexion) . "<br>";
        }



        // Cerrar la conexión
        mysqli_close($conexion);
        ?>
    </body>
</html>
