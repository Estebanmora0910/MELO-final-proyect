<?php
require_once 'conexion.php'; // Aquí se carga el $pdo global

class RegistrarseModel {

    // Verifica si el usuario o correo ya existen
    public function usuarioOCorreoExiste($correo, $usuario) {
        global $pdo; // Usar conexión global

        try {
            // Revisar si el correo existe
            $sqlCorreo = "SELECT COUNT(*) FROM personas WHERE reg_correo = :correo";
            $stmtCorreo = $pdo->prepare($sqlCorreo);
            $stmtCorreo->bindParam(":correo", $correo, PDO::PARAM_STR);
            $stmtCorreo->execute();
            $correoExiste = $stmtCorreo->fetchColumn();

            // Revisar si el usuario existe
            $sqlUsuario = "SELECT COUNT(*) FROM usuario WHERE usu_nombre_usuario = :usuario";
            $stmtUsuario = $pdo->prepare($sqlUsuario);
            $stmtUsuario->bindParam(":usuario", $usuario, PDO::PARAM_STR);
            $stmtUsuario->execute();
            $usuarioExiste = $stmtUsuario->fetchColumn();

            return ($correoExiste > 0 || $usuarioExiste > 0);
        } catch (PDOException $e) {
            throw new Exception("Error al verificar usuario/correo: " . $e->getMessage() . " SQLSTATE: " . $e->getCode());
        }
    }

    // Inserta en personas, usuario y cliente
    public function registrarUsuario($nombre, $correo, $contrasena, $direccion, $ciudad, $telefono, $usuario) {
        global $pdo; // Usar conexión global

        try {
            // Verificar base de datos
            $db = $pdo->query("SELECT DATABASE()")->fetchColumn();
            if ($db !== 'melodatabase') {
                throw new Exception("Conectado a la base de datos incorrecta: $db");
            }

            // Iniciar transacción
            $pdo->beginTransaction();

            // Validar longitud de datos
            if (strlen($contrasena) > 255) {
                throw new Exception("La contraseña hasheada excede los 255 caracteres");
            }
            if (strlen($usuario) > 50) {
                throw new Exception("El nombre de usuario excede los 50 caracteres");
            }

            // Insertar en personas
            $sql_personas = "INSERT INTO personas 
                (reg_nombre, reg_correo, reg_contrasena, reg_direccion, reg_ciudad, reg_telefono, reg_nombre_usuario) 
                VALUES (:nombre, :correo, :contrasena, :direccion, :ciudad, :telefono, :usuario)";
            $stmt_personas = $pdo->prepare($sql_personas);
            $stmt_personas->bindParam(":nombre", $nombre, PDO::PARAM_STR);
            $stmt_personas->bindParam(":correo", $correo, PDO::PARAM_STR);
            $stmt_personas->bindParam(":contrasena", $contrasena, PDO::PARAM_STR);
            $stmt_personas->bindParam(":direccion", $direccion, PDO::PARAM_STR);
            $stmt_personas->bindParam(":ciudad", $ciudad, PDO::PARAM_STR);
            $stmt_personas->bindParam(":telefono", $telefono, PDO::PARAM_STR);
            $stmt_personas->bindParam(":usuario", $usuario, PDO::PARAM_STR);
            if (!$stmt_personas->execute()) {
                throw new Exception("Fallo al insertar en personas: " . print_r($stmt_personas->errorInfo(), true));
            }

            $id_personas = $pdo->lastInsertId();
            if ($id_personas == 0) {
                throw new Exception("ID de personas no generado (posiblemente no AUTO_INCREMENT o inserción fallida)");
            }

            // Verificar que id_personas exista
            $sql_check_personas = "SELECT COUNT(*) FROM personas WHERE id_personas = :id_personas";
            $stmt_check_personas = $pdo->prepare($sql_check_personas);
            $stmt_check_personas->bindParam(":id_personas", $id_personas, PDO::PARAM_INT);
            $stmt_check_personas->execute();
            if ($stmt_check_personas->fetchColumn() == 0) {
                throw new Exception("ID de personas ($id_personas) no encontrado en la tabla personas");
            }

            // Verificar que id_rol=3 exista
            $id_rol = 3; // Asumiendo que 3 es "cliente"
            $sql_check_rol = "SELECT COUNT(*) FROM rol WHERE id_rol = :id_rol";
            $stmt_check_rol = $pdo->prepare($sql_check_rol);
            $stmt_check_rol->bindParam(":id_rol", $id_rol, PDO::PARAM_INT);
            $stmt_check_rol->execute();
            if ($stmt_check_rol->fetchColumn() == 0) {
                $sql_insert_rol = "INSERT INTO rol (id_rol, tipo_rol) VALUES (:id_rol, 'Cliente')";
                $stmt_insert_rol = $pdo->prepare($sql_insert_rol);
                $stmt_insert_rol->bindParam(":id_rol", $id_rol, PDO::PARAM_INT);
                if (!$stmt_insert_rol->execute()) {
                    throw new Exception("Fallo al crear id_rol=3: " . print_r($stmt_insert_rol->errorInfo(), true));
                }
            }

            // Log de parámetros
            error_log("Insertando en usuario: id_personas=$id_personas, id_rol=$id_rol, usuario=$usuario, contrasena=" . substr($contrasena, 0, 10) . "...");

            // Insertar en usuario
            $sql_usuario = "INSERT INTO usuario (id_personas, id_rol, usu_nombre_usuario, usu_contrasena) 
                            VALUES (:id_personas, :id_rol, :usuario, :contrasena)";
            $stmt_usuario = $pdo->prepare($sql_usuario);
            $stmt_usuario->bindParam(":id_personas", $id_personas, PDO::PARAM_INT);
            $stmt_usuario->bindParam(":id_rol", $id_rol, PDO::PARAM_INT);
            $stmt_usuario->bindParam(":usuario", $usuario, PDO::PARAM_STR);
            $stmt_usuario->bindParam(":contrasena", $contrasena, PDO::PARAM_STR);
            if (!$stmt_usuario->execute()) {
                throw new Exception("Fallo al insertar en usuario: " . print_r($stmt_usuario->errorInfo(), true));
            }

            $id_usuario = $pdo->lastInsertId();
            if ($id_usuario == 0) {
                throw new Exception("ID de usuario no generado: " . print_r($stmt_usuario->errorInfo(), true));
            }

            // Verificar que id_usuario exista
            $sql_check_usuario = "SELECT COUNT(*) FROM usuario WHERE id_usuario = :id_usuario";
            $stmt_check_usuario = $pdo->prepare($sql_check_usuario);
            $stmt_check_usuario->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
            $stmt_check_usuario->execute();
            if ($stmt_check_usuario->fetchColumn() == 0) {
                throw new Exception("ID de usuario ($id_usuario) no encontrado en la tabla usuario");
            }

            // Log para cliente
            error_log("Intentando insertar en cliente: id_usuario=$id_usuario, cli_numero_pedidos=0");

            // Verificar que id_usuario sea válido para cliente
            $sql_check_usuario_cliente = "SELECT COUNT(*) FROM usuario WHERE id_usuario = :id_usuario";
            $stmt_check_usuario_cliente = $pdo->prepare($sql_check_usuario_cliente);
            $stmt_check_usuario_cliente->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
            $stmt_check_usuario_cliente->execute();
            if ($stmt_check_usuario_cliente->fetchColumn() == 0) {
                throw new Exception("ID de usuario ($id_usuario) no encontrado para insertar en cliente");
            }

            // Insertar en cliente
            $cli_numero_pedidos = 0;
            $sql_cliente = "INSERT INTO cliente (id_usuario, cli_numero_pedidos) 
                            VALUES (:id_usuario, :cli_numero_pedidos)";
            $stmt_cliente = $pdo->prepare($sql_cliente);
            $stmt_cliente->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
            $stmt_cliente->bindParam(":cli_numero_pedidos", $cli_numero_pedidos, PDO::PARAM_INT);
            if (!$stmt_cliente->execute()) {
                throw new Exception("Fallo al insertar en cliente: " . print_r($stmt_cliente->errorInfo(), true));
            }

            $id_cliente = $pdo->lastInsertId();
            if ($id_cliente == 0) {
                throw new Exception("ID de cliente no generado: " . print_r($stmt_cliente->errorInfo(), true));
            }

            // Verificar que id_cliente exista
            $sql_check_cliente = "SELECT COUNT(*) FROM cliente WHERE id_cliente = :id_cliente";
            $stmt_check_cliente = $pdo->prepare($sql_check_cliente);
            $stmt_check_cliente->bindParam(":id_cliente", $id_cliente, PDO::PARAM_INT);
            $stmt_check_cliente->execute();
            if ($stmt_check_cliente->fetchColumn() == 0) {
                throw new Exception("ID de cliente ($id_cliente) no encontrado en la tabla cliente");
            }

            // Confirmar transacción
            $pdo->commit();
            error_log("Registro exitoso: id_personas=$id_personas, id_usuario=$id_usuario, id_cliente=$id_cliente");
            return true;
        } catch (Exception $e) {
            // Revertir transacción
            $pdo->rollBack();
            // Log para depuración
            error_log("Error en registro: " . $e->getMessage());
            throw $e; // Relanza para el controlador
        }
    }
}