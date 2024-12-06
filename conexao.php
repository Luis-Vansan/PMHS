<?php

    $host = 'localhost';
    $bd = 'phms';
    $user = 'root';
    $password = '';
    $port = 3306;

    $con = mysqli_connect(
        $host,
        $user,
        $password,
        $bd,
        $port
    );

    // se n conectou...
    if ( !$con ) {
        echo 'Conexão falhou!';
        exit;
    }

    

?>