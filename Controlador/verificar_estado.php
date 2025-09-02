<?php
session_start();

// Retorna "1" si hay sesión, "0" si no
if (isset($_SESSION['id_usuario'])) {
    echo "1";
} else {
    echo "0";
}
?>