<?php
require 'conexao.php'; // Inclui a conexão com o banco

// Verifica se foi realizada uma busca ou filtro
$q = "
    SELECT posts.*, GROUP_CONCAT(tags.tipo SEPARATOR ', ') AS tipos
    FROM posts
    LEFT JOIN post_tags ON posts.id = post_tags.id_post
    LEFT JOIN tags ON post_tags.id_tag = tags.id_tag
    WHERE 1=1
";

// Adiciona o filtro de busca por nome, se fornecido
if (isset($_POST['buscado']) && $_POST['buscado'] != "") {
    $buscado = $con->real_escape_string($_POST['buscado']);
    $q .= " AND posts.nome LIKE '%$buscado%'";
}

// Adiciona o filtro de tipo, se selecionado
if (isset($_POST['id_tipo']) && $_POST['id_tipo'] != "") {
    $id_tipo = (int)$_POST['id_tipo']; // Sanitiza o ID
    $q .= " AND post_tags.id_tag = $id_tipo";
}

// Agrupa e ordena os resultados
$q .= "
    GROUP BY posts.id
    ORDER BY posts.data_post DESC
";

// Executa a consulta
$result = $con->query($q);

// Consulta para listar os tipos de tags disponíveis
$query_tags = "SELECT * FROM tags";
$result_tags = $con->query($query_tags);

if (!$result) {
    die("Erro na consulta de posts: " . $con->error);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feed Principal</title>
    <link rel="stylesheet" href="feed.css">
</head>
<body>
    <header style="margin-bottom: 30px;">
        <a href="home.php" class="logo">
            <img src="pmhs.png" alt="PMHS Logo">
        </a>
    </header>

    <!-- Barra de busca e filtro -->
    <div class="search-container">
        <form action="" method="post" class="search-form">
            <div class="search-group">
                <input type="text" name="buscado" placeholder="Buscar por nome do post" value="<?= isset($_POST['buscado']) ? htmlspecialchars($_POST['buscado']) : '' ?>" class="search-bar">

                <!-- Seleção de tipo -->
                <select name="id_tipo" class="search-filter">
                    <option value="">Todos os Tipos</option>
                    <?php if ($result_tags->num_rows > 0): ?>
                        <?php while ($tag = $result_tags->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($tag['id_tag']) ?>" <?= isset($_POST['id_tipo']) && $_POST['id_tipo'] == $tag['id_tag'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($tag['tipo']) ?>
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>

            <button type="submit" class="search-button">Buscar</button>
        </form>
    </div>

    <!-- Exibição de resultados -->
    <div>
        <?php if (isset($_POST['buscado']) && $_POST['buscado'] != ""): ?>
            <!-- Botão Voltar -->
            <div style="margin-top: 20px; text-align: center;">
                <a href="feed.php">
                <strong>Voltar</strong>
                </a>
            </div>
        <?php endif; ?>

        <div class="container">
            <h2>Resultados</h2>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="post">
                        <h3><?= htmlspecialchars($row['nome']) ?></h3>
                        <p><?= nl2br(htmlspecialchars($row['conteudo'])) ?></p>
                        <p><strong>Tipo(s):</strong> <?= htmlspecialchars($row['tipos']) ?></p>
                        <p class="timestamp">Publicado em: <?= date('d/m/Y H:i', strtotime($row['data_post'])) ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Nenhuma publicação encontrada.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>