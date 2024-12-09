<?php
require 'conexao.php'; // Inclui a conexão com o banco

// Verifica se o método de requisição é POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'])) {
        $id_post = (int)$_POST['id'];
        $nome = $_POST['nome'];
        $conteudo = $_POST['conteudo'];
        $imagem_url = $_POST['imagem_url'];
        $fonte = isset($_POST['fonte']) && $_POST['fonte'] !== '' ? $_POST['fonte'] : NULL;
        $tipos = isset($_POST['tipos']) ? $_POST['tipos'] : [];
        $media_type = $_POST['media_type'];
        $video_url = ($media_type === 'youtube') ? $_POST['video_url'] : NULL;
        $gif_url = ($media_type === 'gif') ? $_POST['gif_url'] : NULL;

        // Prepara a consulta SQL com parâmetros
        $stmt = $con->prepare("UPDATE posts SET nome = ?, conteudo = ?, imagem_url = ?, video_url = ?, gif_url = ?, media_type = ?, fonte = ? WHERE id = ?");
        $stmt->bind_param('sssssssi', $nome, $conteudo, $imagem_url, $video_url, $gif_url, $media_type, $fonte, $id_post);

        if ($stmt->execute()) {
            // Primeiro, exclui as tags antigas associadas ao post
            $con->query("DELETE FROM post_tags WHERE id_post = $id_post");

            // Insere as novas tags associadas ao post
            $stmt_tag = $con->prepare("INSERT INTO post_tags (id_post, id_tag) VALUES (?, ?)");
            foreach ($tipos as $id_tag) {
                $id_tag = (int)$id_tag;
                $stmt_tag->bind_param('ii', $id_post, $id_tag);
                $stmt_tag->execute();
            }

            $stmt_tag->close();
            echo "Post atualizado com sucesso.";
            header("Location: feedadm.php");
            exit();
        } else {
            echo "<p>Erro ao atualizar o post: " . $stmt->error . "</p>";
        }

        $stmt->close();
    } else {
        echo "ID do post não especificado.";
    }
} else {
    echo "Método de requisição inválido.";
}

// Fecha a conexão
$con->close();
?>
