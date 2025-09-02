<?php
require_once __DIR__ . '/../Modelo/PersonaModel.php';

// Editar persona (solo rol)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_persona'])) {
    $id = $_POST['id_personas'];
    $datos = [
        'id_rol' => $_POST['id_rol']
    ];
    PersonasModel::actualizarPersona($id, $datos);
    header('Location: /melo8-main/Controlador/PersonasController.php');
    exit;
}

// Mostrar formulario de edición
if (isset($_GET['id'])) {
    $persona = PersonasModel::obtenerPersonaPorId($_GET['id']);
    $roles = PersonasModel::obtenerRoles();
    include __DIR__ . '/../Vista/html/editar_persona.php';
    exit;
}

// Listar todas las personas con filtros
$filtro = $_GET['filtro'] ?? 'todos';
$rol = $_GET['rol'] ?? null;
$search = $_GET['search'] ?? null;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 10;

$personas = PersonasModel::obtenerPersonas($filtro, $rol, $search, $page, $perPage);
$totalPersonas = PersonasModel::obtenerTotalPersonas($filtro, $rol, $search);
$totalPages = ceil($totalPersonas / $perPage);

$roles = PersonasModel::obtenerRoles(); // Para el select de roles en la vista

include __DIR__ . '/../Vista/html/gestion_personas.php';