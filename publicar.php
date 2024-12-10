<?php 
require 'conexao.php'; // Inclui a conexão com o banco

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $con->real_escape_string($_POST['nome']);
    $conteudo = $con->real_escape_string($_POST['conteudo']);
    $imagem_url = $con->real_escape_string($_POST['imagem_url']);
    $fonte = $con->real_escape_string($_POST['fonte']);
    $media_type = $con->real_escape_string($_POST['media_type']);
    
    // Inicializa as URLs como null
    $video_url = null;
    $gif_url = null;
    
    // Define a URL apropriada baseada no tipo de mídia
    if ($media_type === 'youtube') {
        $video_url = $con->real_escape_string($_POST['video_url']);
    } elseif ($media_type === 'gif') {
        $gif_url = $con->real_escape_string($_POST['gif_url']);
    }
    
    $tipos = $_POST['tipos'];

    $sql = "INSERT INTO posts (nome, conteudo, imagem_url, video_url, gif_url, media_type, fonte) 
            VALUES ('$nome', '$conteudo', '$imagem_url', " . 
            ($video_url ? "'$video_url'" : "NULL") . ", " .
            ($gif_url ? "'$gif_url'" : "NULL") . ", " .
            "'$media_type', '$fonte')";
    if ($con->query($sql) === TRUE) {
        $id_post = $con->insert_id; // ID do post recém-inserido

        // Insere os tipos relacionados ao post
        foreach ($tipos as $id_tag) {
            $id_tag = (int)$id_tag;
            $con->query("INSERT INTO post_tags (id_post, id_tag) VALUES ($id_post, $id_tag)");
        }

        header("Location: feedadm.php");
        exit();
    } else {
        echo "<p>Erro ao publicar o post: " . $con->error . "</p>";
    }
}

// Busca os tipos de tags para exibir no formulário
$sql_tags = "SELECT id_tag, tipo FROM tags";
$result_tags = $con->query($sql_tags);

if (!$result_tags) {
    die("Erro ao buscar tags: " . $con->error);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicar Novo Post</title>
    <link rel="stylesheet" href="feed.css">
</head>
<body>
    <header>Publicar Novo Post</header>
    <div class="container centro">
    <form method="POST" action="">
        <div>
            <br>
            <input type="text" name="nome" placeholder="Nome do exercício" style="width: 200px; padding: 5px; font-size: 14px;" required>
            <br><br>
            <textarea class="textinho" name="conteudo" placeholder="Sobre" rows="7" style="width: 400px; padding: 5px; font-size: 14px;" required></textarea>
            <br><br>
            <input type="text" name="imagem_url" placeholder="URL da Imagem" style="width: 400px; padding: 5px; font-size: 14px;" required>
            <br><br>
            <input type="text" name="fonte" placeholder="Fonte (URL)" style="width: 400px; padding: 5px; font-size: 14px;">
            <br><br>
        </div>
        
        <div class="media-selection" style="margin-bottom: 20px;">
            <strong>Escolha o tipo de mídia:</strong><br>
            <label>
                <input type="radio" name="media_type" value="none" checked> Nenhuma mídia
            </label><br>
            <label>
                <input type="radio" name="media_type" value="gif"> GIF
            </label><br>
            <label>
                <input type="radio" name="media_type" value="youtube"> Vídeo do YouTube
            </label>
        </div>

        <div id="gif_input" style="display: none;">
            <input type="text" name="gif_url" placeholder="URL do GIF" style="width: 400px; padding: 5px; font-size: 14px;">
            <br><br>
        </div>

        <div id="youtube_input" style="display: none;">
            <input type="text" name="video_url" placeholder="URL do vídeo do YouTube" style="width: 400px; padding: 5px; font-size: 14px;">
            <br><br>
        </div>
        <div class="fonte_branca">
            <strong><p style="font-size: 25px;">Selecione os tipos:</p></strong>
            <?php while ($tag = $result_tags->fetch_assoc()): ?>
                <label>
                    <input type="checkbox" name="tipos[]" value="<?= $tag['id_tag'] ?>"> 
                    <?= htmlspecialchars($tag['tipo']) ?>
                </label><br>
            <?php endwhile; ?>
        </div>
        <div class="mtop">
            <button type="submit">Publicar</button>
        </div>
    </form>

        <p><a href="feedadm.php">Voltar ao Feed</a></p>
    </div>
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
</body>
</html>
