<?php
// Credenciais do banco de dados
$host = 'yww1u.h.filess.io'; // Exemplo: 'localhost' ou '127.0.0.1'
$port = '3307'; // Geralmente '3306'
$dbname = 'PMHS_toyframein'; // Nome do banco de dados
$username = 'PMHS_toyframein'; // Nome do usuário do banco
$password = 'e5a4b7b285afc699fa7838010508ddccf34da001'; // Senha do banco

// Conexão usando mysqli
$con = new mysqli($host, $username, $password, $dbname, $port);

// Verifica se houve erro na conexão
if ($con->connect_error) {
    // Exibe mensagem de erro no console do navegador
    echo "<script>console.error('Erro ao conectar ao banco de dados: " . addslashes($con->connect_error) . "');</script>";
    exit;
}

// Se a conexão for bem-sucedida, exibe a mensagem no console do navegador
echo "<script>console.log('Conexão realizada com sucesso!');</script>";
?>
