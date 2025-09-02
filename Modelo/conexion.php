<?php
$link = 'mysql:host=localhost;dbname=asdd';
$usuario = 'root';
$pass = '';

try {
    $pdo = new PDO($link, $usuario, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    // Verificar conexión ejecutando una consulta simple
    $pdo->query("SELECT 1");
} catch (PDOException $e) {
    error_log("Error de conexión a la base de datos: " . $e->getMessage());
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
?>