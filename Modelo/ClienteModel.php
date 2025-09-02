 <?php
    require_once __DIR__ . '/conexion.php';

    class ClienteModel {
        public static function obtenerClientes($search = null, $city = null, $minOrders = null, $page = 1, $perPage = 10) {
            global $pdo;
            $offset = (int)(($page - 1) * $perPage);
            $perPage = (int)$perPage;

            $sql = "
                SELECT c.id_cliente, p.reg_nombre AS nombre, p.reg_correo AS correo, p.reg_ciudad AS ciudad, 
                    u.usu_nombre_usuario AS usuario, c.cli_numero_pedidos AS numero_pedidos
                FROM cliente c
                JOIN usuario u ON c.id_usuario = u.id_usuario
                JOIN personas p ON u.id_personas = p.id_personas
                JOIN rol r ON u.id_rol = r.id_rol
                WHERE r.tipo_rol = 'Cliente'";
            
            $params = [];
            $whereClauses = [];

            if ($search) {
                $search = "%$search%";
                $whereClauses[] = "(p.reg_nombre LIKE ? OR p.reg_correo LIKE ? OR u.usu_nombre_usuario LIKE ?)";
                $params[] = $search;
                $params[] = $search;
                $params[] = $search;
            }
            if ($city) {
                $whereClauses[] = "p.reg_ciudad LIKE ?";
                $params[] = "%$city%";
            }
            if ($minOrders !== null && $minOrders >= 0) {
                $whereClauses[] = "c.cli_numero_pedidos >= ?";
                $params[] = $minOrders;
            }

            if (!empty($whereClauses)) {
                $sql .= " AND " . implode(" AND ", $whereClauses);
            }

            $sql .= " ORDER BY c.id_cliente LIMIT ? OFFSET ?";

            try {
                $stmt = $pdo->prepare($sql);
                $paramIndex = 1;
                foreach ($params as $value) {
                    $stmt->bindValue($paramIndex, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
                    $paramIndex++;
                }
                $stmt->bindValue($paramIndex, $perPage, PDO::PARAM_INT);
                $stmt->bindValue($paramIndex + 1, $offset, PDO::PARAM_INT);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("Error en obtenerClientes: " . $e->getMessage() . " | SQL: " . $sql . " | Params: " . json_encode(array_merge($params, [$perPage, $offset])));
                throw new Exception("Error al ejecutar la consulta de clientes: " . $e->getMessage());
            }
        }

        public static function obtenerTotalClientes($search = null, $city = null, $minOrders = null) {
            global $pdo;
            $sql = "
                SELECT COUNT(DISTINCT c.id_cliente) as total
                FROM cliente c
                JOIN usuario u ON c.id_usuario = u.id_usuario
                JOIN personas p ON u.id_personas = p.id_personas
                JOIN rol r ON u.id_rol = r.id_rol
                WHERE r.tipo_rol = 'Cliente'";
            
            $params = [];
            $whereClauses = []; 
            if ($search) {
                $search = "%$search%";
                $whereClauses[] = "(p.reg_nombre LIKE ? OR p.reg_correo LIKE ? OR u.usu_nombre_usuario LIKE ?)";
                $params[] = $search;
                $params[] = $search;
                $params[] = $search;
            }
            if ($city) {
                $whereClauses[] = "p.reg_ciudad LIKE ?";
                $params[] = "%$city%";
            }
            if ($minOrders !== null && $minOrders >= 0) {
                $whereClauses[] = "c.cli_numero_pedidos >= ?";
                $params[] = $minOrders;
            }

            if (!empty($whereClauses)) {
                $sql .= " AND " . implode(" AND ", $whereClauses);
            }

            try {
                $stmt = $pdo->prepare($sql);
                $paramIndex = 1;
                foreach ($params as $value) {
                    $stmt->bindValue($paramIndex, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
                    $paramIndex++;
                }
                $stmt->execute();
                return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            } catch (PDOException $e) {
                error_log("Error en obtenerTotalClientes: " . $e->getMessage() . " | SQL: " . $sql . " | Params: " . json_encode($params));
                throw new Exception("Error al contar clientes: " . $e->getMessage());
            }
        }
    }
    ?>