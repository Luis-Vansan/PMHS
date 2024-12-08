<?php
require 'conexao.php'; // Inclui a conexão com o banco

// Verificar se o parâmetro `id` foi passado na URL
if (isset($_GET['id'])) {
    $id_post = (int)$_GET['id'];

    // Buscar o post para preenchimento do formulário
    $sql_post = "SELECT * FROM posts WHERE id = ?";
    $stmt = $con->prepare($sql_post);

    if ($stmt) {
        $stmt->bind_param("i", $id_post);
        $stmt->execute();
        $result_post = $stmt->get_result();

        if ($result_post->num_rows > 0) {
            $post = $result_post->fetch_assoc();
        } else {
            echo "<p>Post não encontrado.</p>";
            exit;
        }

        $stmt->close();
    } else {
        echo "<p>Erro na preparação da consulta: " . $con->error . "</p>";
        exit;
    }

    // Buscar tags associadas ao post
    $sql_tags_post = "SELECT id_tag FROM post_tags WHERE id_post = ?";
    $stmt_tags = $con->prepare($sql_tags_post);

    if ($stmt_tags) {
        $stmt_tags->bind_param("i", $id_post);
        $stmt_tags->execute();
        $result_tags_post = $stmt_tags->get_result();
        $tags_associadas = [];
        while ($tag = $result_tags_post->fetch_assoc()) {
            $tags_associadas[] = $tag['id_tag'];
        }
        $stmt_tags->close();
    } else {
        echo "<p>Erro na preparação da consulta de tags associadas: " . $con->error . "</p>";
        exit;
    }

    // Buscar todas as tags disponíveis
    $sql_tags = "SELECT id_tag, tipo FROM tags";
    $result_tags = $con->query($sql_tags);

    if (!$result_tags) {
        die("<p>Erro ao buscar tags: " . $con->error . "</p>");
    }
} else {
    echo "<p>ID do post não especificado.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Post</title>
    <link rel="stylesheet" href="feed.css">
</head>
<body>
    <header>Editar Post</header>
    <div class="container centro">
        <form method="POST" action="atualizar.php">
            <input type="hidden" name="id" value="<?= $post['id']; ?>">
            <input type="text" name="nome" placeholder="Seu nome ou apelido" value="<?= htmlspecialchars($post['nome']); ?>" style="width: 200px; padding: 5px; font-size: 14px;" required>
            <br><br>
            <textarea class="textinho" name="conteudo" placeholder="Sobre" rows="7" style="width: 400px; padding: 5px; font-size: 14px;" required><?= htmlspecialchars($post['conteudo']); ?></textarea>
            <br><br>
            <input type="text" name="imagem_url" placeholder="URL da Imagem" value="<?= htmlspecialchars($post['imagem_url']); ?>" style="width: 400px; padding: 5px; font-size: 14px;" required>
            <br><br>
            <div class="fonte_branca">
                <strong><p style="font-size: 25px;">Selecione os tipos:</p></strong>
                <?php while ($tag = $result_tags->fetch_assoc()): ?>
                    <label>
                        <input type="checkbox" name="tipos[]" value="<?= $tag['id_tag'] ?>" 
                        <?= in_array($tag['id_tag'], $tags_associadas) ? 'checked' : ''; ?>>
                        <?= htmlspecialchars($tag['tipo']); ?>
                    </label><br>
                <?php endwhile; ?>
            </div>
            <div class="mtop">
                <button type="submit">Atualizar</button>
            </div>
        </form>
        <p><a href="feedadm.php">Voltar ao Feed</a></p>
    </div>
</body>
</html>
