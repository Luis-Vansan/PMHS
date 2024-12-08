<?php
    require 'conexao.php'; // Inclui a conexão com o banco

    // Verifica se o método de requisição é POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['id'])) {
            $id_post = (int)$_POST['id'];
            $nome = $con->real_escape_string($_POST['nome']);
            $conteudo = $con->real_escape_string($_POST['conteudo']);
            $imagem_url = $con->real_escape_string($_POST['imagem_url']);
            // Verifica se o campo 'fonte' foi enviado e não está vazio
            $fonte = isset($_POST['fonte']) && $_POST['fonte'] !== '' ? "'" . $con->real_escape_string($_POST['fonte']) . "'" : "NULL";

            $tipos = isset($_POST['tipos']) ? $_POST['tipos'] : [];

    
            // Atualiza o post na tabela `posts`
            $sql = "UPDATE posts SET nome = '$nome', conteudo = '$conteudo', imagem_url = '$imagem_url', fonte = $fonte WHERE id = $id_post";

            if ($con->query($sql) === TRUE) {
                // Primeiro, exclui as tags antigas associadas ao post
                $con->query("DELETE FROM post_tags WHERE id_post = $id_post");
    
                // Insere as novas tags associadas ao post
                foreach ($tipos as $id_tag) {
                    $id_tag = (int)$id_tag;
                    $con->query("INSERT INTO post_tags (id_post, id_tag) VALUES ($id_post, $id_tag)");
                }
    
                echo "Post atualizado com sucesso.";
                header("Location: feedadm.php"); // Redireciona após a atualização
                exit();
            } else {
                echo "<p>Erro ao atualizar o post: " . $con->error . "</p>";
            }
        } else {
            echo "ID do post não especificado.";
        }
    } else {
        echo "Método de requisição inválido.";
    }
    
    // Fecha a conexão
    $con->close();
    
?>