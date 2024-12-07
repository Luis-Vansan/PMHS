<?php
require 'conexao.php'; // Inclui a conexão com o banco

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $con->real_escape_string($_POST['nome']);
    $conteudo = $con->real_escape_string($_POST['conteudo']);
    $tipos = $_POST['tipos']; // Array com os IDs das tags selecionadas

    // Insere o post
    $sql = "INSERT INTO posts (nome, conteudo) VALUES ('$nome', '$conteudo')";
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
            <input type="text" name="nome" placeholder="Seu nome ou apelido" style="width: 200px; padding: 5px; font-size: 14px;" required>
            <br>
            <br>
            <textarea class="textinho" name="conteudo" placeholder="Sobre" rows="7" style="width: 400px; padding: 5px; font-size: 14px;" required></textarea>
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
</body>
</html>
