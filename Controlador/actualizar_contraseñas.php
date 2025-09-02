<?php
require_once '../Modelo/conexion.php'; // Ajusta la ruta si es necesario

function actualizarContrasenasNoCifradas($tabla, $columna, $id_columna, $pdo) {
    // Seleccionar los registros con contraseñas sin cifrar
    $stmt = $pdo->prepare("SELECT $id_columna, $columna FROM $tabla WHERE LENGTH($columna) < 50 OR $columna NOT LIKE '\$2%'");
    $stmt->execute();
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($usuarios as $usuario) {
        $id = $usuario[$id_columna];
        $contrasena = $usuario[$columna];

        // Cifrar la contraseña solo si no está vacía
        if (!empty($contrasena)) {
            $hash = password_hash($contrasena, PASSWORD_DEFAULT);

            $update = $pdo->prepare("UPDATE $tabla SET $columna = ? WHERE $id_columna = ?");
            $update->execute([$hash, $id]);

            echo "Contraseña actualizada para $tabla ID $id<br>";
        }
    }
}

try {
    actualizarContrasenasNoCifradas('usuario', 'usu_contrasena', 'id_usuario', $pdo);
    actualizarContrasenasNoCifradas('personas', 'reg_contrasena', 'id_personas', $pdo);

    echo "<br>✅ Todas las contraseñas no cifradas fueron actualizadas.";
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
