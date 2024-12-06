<?php
// Inclua a conexão com o banco de dados
include 'conexao.php';

// Defina os dados do usuário admin
$nome = 'Admin';
$email = 'admin@pmhs.com';
$senha = 'Erikadecalcinha2'; // Senha em texto plano (nunca armazene assim em produção)

// Crie o hash da senha
$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

// Prepare a consulta SQL para inserir o usuário admin
$query = "INSERT INTO usuarios (nome, email, senha, admin) VALUES ('$nome', '$email', '$senhaHash', 1)";

// Execute a consulta e verifique se foi bem-sucedida
if (mysqli_query($con, $query)) {
    echo "Usuário admin criado com sucesso!";
} else {
    echo "Erro ao criar usuário admin: " . mysqli_error($con);
}

// Feche a conexão
mysqli_close($con);
?>
