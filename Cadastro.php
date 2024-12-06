<?php
session_start();
include 'conexao.php'; // Inclua a conexão com o banco de dados

$message = ''; // Inicializa uma variável para mensagens

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica se as chaves existem no array $_POST
    if (isset($_POST['nome'], $_POST['email'], $_POST['senha'], $_POST['confirmarSenha'])) {
        $nome = mysqli_real_escape_string($con, trim($_POST['nome']));
        $email = mysqli_real_escape_string($con, trim($_POST['email']));
        $senha = mysqli_real_escape_string($con, trim($_POST['senha']));
        $confirmarSenha = mysqli_real_escape_string($con, trim($_POST['confirmarSenha']));

        // Validar se as senhas correspondem
        if ($senha !== $confirmarSenha) {
            $_SESSION['message'] = "As senhas não coincidem.";
        } elseif (empty($nome) || empty($email) || empty($senha)) {
            $_SESSION['message'] = "Por favor, preencha todos os campos.";
        } else {
            // Verificar se o email já existe
            $emailCheckQuery = "SELECT * FROM usuarios WHERE email = '$email'";
            $result = mysqli_query($con, $emailCheckQuery);

            if (mysqli_num_rows($result) > 0) {
                $_SESSION['message'] = "Este email já está cadastrado. Tente outro.";
            } else {    
                // Criptografar a senha antes de armazená-la
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

                // Prepare a consulta SQL para inserir os dados
                $query = "INSERT INTO usuarios (nome, email, senha) VALUES ('$nome', '$email', '$senhaHash')";

                // Executa a consulta
                if (mysqli_query($con, $query)) {
                    $_SESSION['message'] = "Cadastro realizado com sucesso!";
                    header('Location: Login.php');
                    exit();
                } else {
                    $_SESSION['message'] = "Erro: " . mysqli_error($con);
                }
            }
        }

        // Redireciona para evitar reenvio do formulário
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }
}

// Fecha a conexão
mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Cadastro.css">
    <title>PHMS</title>
</head>

<body>
    <div class="box">
        <div class="img-box">
            <img src="logo_cadastro2.png">
        </div>
        <div class="form-box">
            <h2>Criar Conta</h2>
            <p> Já é um membro? <a href="Login.php"> Login </a> </p>

            <?php if (!empty($_SESSION['message'])): ?>
                <div id="message" class="message <?php echo strpos($_SESSION['message'], 'sucesso') !== false ? 'success' : 'error'; ?>">
                    <?php 
                        echo $_SESSION['message']; 
                        // Limpa a mensagem após exibição
                        unset($_SESSION['message']);
                    ?>
                </div>
            <?php endif; ?>

            <form id="formCadastro" action="" method="post">
                <div class="input-group">
                    <label for="nome"> Nome Completo</label>
                    <input type="text" id="nome" name="nome" placeholder="Digite o seu nome completo" required>
                </div>

                <div class="input-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" placeholder="Digite o seu email" required>
                </div>

                <div class="input-group w50">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>
                </div>

                <div class="input-group w50">
                    <label for="confirmarSenha">Confirmar Senha</label>
                    <input type="password" id="confirmarSenha" name="confirmarSenha" placeholder="Confirme a senha" required>
                </div>

                <div class="input-group">
                    <button type="submit">Cadastrar</button>
                </div>
            </form>
        </div>
    </div>
    <script src="validacao.js"></script>
</body>
</html>
