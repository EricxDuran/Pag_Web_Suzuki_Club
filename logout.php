<?php
session_start();
session_destroy(); // Borra la sesión
header("Location: index.php"); // Te devuelve al inicio
exit();
?>