<?php
require_once '../Modelo/AdminModel.php';

class AdminController {
    private $adminModel;

    public function __construct() {
        $this->adminModel = new AdminModel();
    }

    public function updateRole() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_personas = $_POST['id_personas'];
            $id_rol = $_POST['id_rol'];
            if ($this->adminModel->updateUserRole($id_personas, $id_rol)) {
                header("Location: ../Vista/html/administrador.php");
                exit();
            } else {
                echo "Error al actualizar el rol.";
            }
        }
    }
}


$controller = new AdminController();
$controller->updateRole();
?>
