<?php
session_start();
session_destroy(); // Destroi a sessão
header('Location: home.php'); // Redireciona para o login
exit();
?>