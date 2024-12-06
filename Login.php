<?php
session_start(); // Inicia a sessão
include 'conexao.php'; // Inclui a conexão com o banco de dados

$message = ''; // Inicializa uma variável para mensagens

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($con, trim($_POST['email']));
    $senha = mysqli_real_escape_string($con, trim($_POST['senha']));

    // Prepare a consulta SQL para buscar o usuário
    $query = "SELECT * FROM usuarios WHERE email = '$email'";
    $resultado = mysqli_query($con, $query);

    // Verifica se o usuário existe
    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $usuario = mysqli_fetch_assoc($resultado);

        // Verifica a senha
        if (password_verify($senha, $usuario['senha'])) {
            // Se a senha estiver correta, armazena informações na sessão
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['nome'] = $usuario['nome'];
            $_SESSION['admin'] = $usuario['admin']; // Armazena o status de administrador

            // Redireciona para a página de admin ou de usuário padrão
            if ($_SESSION['admin'] == 1) {
                header('Location: feedadm.php'); // Página do admin
            } else {
                header('Location: home.php'); // Página do usuário padrão
            }
            exit();
        } else {
            $_SESSION['message'] = "Senha incorreta."; // Armazena a mensagem na sessão
        }
    } else {
        $_SESSION['message'] = "Usuário não encontrado."; // Armazena a mensagem na sessão
    }

    // Redireciona para evitar reenvio do formulário
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
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
    <link rel="stylesheet" href="Login.css">
    <title>PHMS</title>
</head>

<body>
    <div class="box">
        <div class="img-box">
            <img src="logo_cadastro2.png">
        </div>
        <div class="form-box">
            <h2>Login</h2>
            <p> Não tem uma conta? <a href="Cadastro.php"> Cadastrar </a> </p>

            <?php if (!empty($_SESSION['message'])): ?>
                <div id="message" class="message <?php echo strpos($_SESSION['message'], 'sucesso') !== false ? 'success' : 'error'; ?>">
                    <?php 
                        echo $_SESSION['message']; 
                        // Limpa a mensagem após exibição
                        unset($_SESSION['message']);
                    ?>
                </div>
            <?php endif; ?>

            <form id="formLogin" action="" method="POST">
                <div class="input-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" placeholder="Digite o seu email" required>
                </div>

                <div class="input-group">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>
                </div>

                <div class="input-group">
                    <button>Login</button>
                </div>
            </form>
        </div>
    </div>
    <script src="validacao.js"></script>
</body>
</html>
