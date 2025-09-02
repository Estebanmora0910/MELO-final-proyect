<?php
session_start();
session_unset();    // Elimina todas las variables de sesión
session_destroy();  // Destruye la sesión

// Redirige al login o al inicio público
header('Location: /melo8-main/Vista/html/login.php');
exit;
