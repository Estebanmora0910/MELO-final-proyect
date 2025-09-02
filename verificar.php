<?php
// Incluir archivo de conexión
require_once __DIR__. '../Modelo/conexion.php';

// Habilitar errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    // Obtener conexión PDO
    $pdo = $GLOBALS['pdo'];

    // Paso 1: Modificar la columna reg_contrasena en personas a VARCHAR(255)
    $sqlAlterPersonas = "ALTER TABLE personas MODIFY reg_contrasena VARCHAR(255) NOT NULL";
    $pdo->exec($sqlAlterPersonas);
    echo "Columna reg_contrasena en personas modificada a VARCHAR(255).<br>";

    // Paso 2: Modificar la columna usu_contrasena en usuario a VARCHAR(255)
    $sqlAlterUsuario = "ALTER TABLE usuario MODIFY usu_contrasena VARCHAR(255) NOT NULL";
    $pdo->exec($sqlAlterUsuario);
    echo "Columna usu_contrasena en usuario modificada a VARCHAR(255).<br>";

    // Paso 3: Obtener todos los usuarios con sus contraseñas
    $sql = "
        SELECT u.id_usuario, u.usu_nombre_usuario, u.usu_contrasena, u.id_personas, p.reg_nombre, p.reg_contrasena
        FROM usuario u
        JOIN personas p ON u.id_personas = p.id_personas
    ";
    $stmt = $pdo->query($sql);
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Verificar si se encontraron usuarios
    if (empty($usuarios)) {
        die("No se encontraron usuarios en la base de datos.");
    }

    // Paso 4: Procesar usuarios y rehashear contraseñas
    $actualizados = 0;
    $errores = 0;
    $noNecesitanActualizar = 0;

    foreach ($usuarios as $usuario) {
        $id_usuario = $usuario['id_usuario'];
        $id_personas = $usuario['id_personas'];
        $usu_nombre_usuario = $usuario['usu_nombre_usuario'];
        $usu_contrasena = $usuario['usu_contrasena'];
        $reg_nombre = $usuario['reg_nombre'];
        $reg_contrasena = $usuario['reg_contrasena'];

        // Verificar si la contraseña en usu_contrasena es un hash válido
        $esHashValido = strlen($usu_contrasena) >= 60 && preg_match('/^\$2[ayb]\$.{56}$/', $usu_contrasena);

        // Si el hash es válido, no necesita actualización
        if ($esHashValido) {
            echo "El usuario '$usu_nombre_usuario' (persona: '$reg_nombre') ya tiene un hash válido: $usu_contrasena<br>";
            $noNecesitanActualizar++;
            continue;
        }

        // Verificar que reg_contrasena no esté vacía
        if (empty($reg_contrasena)) {
            echo "Advertencia: No se encontró contraseña en texto plano para el usuario '$usu_nombre_usuario' (persona: '$reg_nombre').<br>";
            $errores++;
            continue;
        }

        // Generar nuevo hash basado en reg_contrasena
        $nuevoHash = password_hash($reg_contrasena, PASSWORD_DEFAULT);

        // Actualizar la contraseña en la tabla personas
        $sqlUpdatePersonas = "UPDATE personas SET reg_contrasena = :hash WHERE id_personas = :id_personas";
        $stmtUpdatePersonas = $pdo->prepare($sqlUpdatePersonas);
        $stmtUpdatePersonas->execute([
            ':hash' => $nuevoHash,
            ':id_personas' => $id_personas
        ]);

        // Actualizar la contraseña en la tabla usuario
        $sqlUpdateUsuario = "UPDATE usuario SET usu_contrasena = :hash WHERE id_usuario = :id_usuario";
        $stmtUpdateUsuario = $pdo->prepare($sqlUpdateUsuario);
        $stmtUpdateUsuario->execute([
            ':hash' => $nuevoHash,
            ':id_usuario' => $id_usuario
        ]);

        // Verificar si ambas actualizaciones fueron exitosas
        if ($stmtUpdatePersonas->rowCount() > 0 && $stmtUpdateUsuario->rowCount() > 0) {
            echo "Contraseña rehasheada para el usuario '$usu_nombre_usuario' (persona: '$reg_nombre'): $nuevoHash<br>";
            $actualizados++;
        } else {
            echo "Error al rehashear la contraseña para el usuario '$usu_nombre_usuario' (persona: '$reg_nombre').<br>";
            $errores++;
        }
    }

    // Paso 5: Mostrar resumen
    echo "<br>Resumen:<br>";
    echo "Usuarios procesados: " . count($usuarios) . "<br>";
    echo "Contraseñas rehasheadas: $actualizados<br>";
    echo "Usuarios con hashes válidos (sin cambios): $noNecesitanActualizar<br>";
    echo "Errores encontrados: $errores<br>";

} catch (PDOException $e) {
    die("Error en la operación: " . $e->getMessage());
}

