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
            <div>
                <input type="hidden" name="id" value="<?= $post['id']; ?>">
                <input type="text" name="nome" placeholder="titulo ou nome" value="<?= htmlspecialchars($post['nome']); ?>" style="width: 200px; padding: 5px; font-size: 14px;" required>
                <br><br>
                <textarea class="textinho" name="conteudo" placeholder="Sobre" rows="7" style="width: 400px; padding: 5px; font-size: 14px;" required><?= htmlspecialchars($post['conteudo']); ?></textarea>
                <br><br>
                <input type="text" name="imagem_url" placeholder="URL da Imagem" value="<?= htmlspecialchars($post['imagem_url']); ?>" style="width: 400px; padding: 5px; font-size: 14px;" required>
                <br><br>
                <input type="text" name="fonte" placeholder="Link do Post" value="<?= htmlspecialchars($post['fonte']); ?>" style="width: 400px; padding: 5px; font-size: 14px;">
                <br><br>
            </div>
            
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
            <div class="media-selection" style="margin-bottom: 20px;">
                <strong>Escolha o tipo de mídia:</strong><br>
                <label>
                    <input type="radio" name="media_type" value="none" <?= $post['media_type'] === 'none' ? 'checked' : '' ?>> Nenhuma mídia
                </label><br>
                <label>
                    <input type="radio" name="media_type" value="gif" <?= $post['media_type'] === 'gif' ? 'checked' : '' ?>> GIF
                </label><br>
                <label>
                    <input type="radio" name="media_type" value="youtube" <?= $post['media_type'] === 'youtube' ? 'checked' : '' ?>> Vídeo do YouTube
                </label>
            </div>

            <div id="gif_input" style="display: <?= $post['media_type'] === 'gif' ? 'block' : 'none' ?>;">
                <input type="text" name="gif_url" placeholder="URL do GIF" value="<?= htmlspecialchars($post['gif_url'] ?? '') ?>" style="width: 400px; padding: 5px; font-size: 14px;">
                <br><br>
            </div>

            <div id="youtube_input" style="display: <?= $post['media_type'] === 'youtube' ? 'block' : 'none' ?>;">
                <input type="text" name="video_url" placeholder="URL do vídeo do YouTube" value="<?= htmlspecialchars($post['video_url'] ?? '') ?>" style="width: 400px; padding: 5px; font-size: 14px;">
                <br><br>
            </div>

            <!-- Mesmo script do publicar.php -->
            <script>
            document.querySelectorAll('input[name="media_type"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    document.getElementById('gif_input').style.display = 'none';
                    document.getElementById('youtube_input').style.display = 'none';
                    
                    if (this.value === 'gif') {
                        document.getElementById('gif_input').style.display = 'block';
                    } else if (this.value === 'youtube') {
                        document.getElementById('youtube_input').style.display = 'block';
                    }
                });
            });
            </script>
            <div class="mtop">
                <button type="submit">Atualizar</button>
            </div>
        </form>
        <p><a href="feedadm.php">Voltar ao Feed</a></p>
    </div>
</body>
</html>
