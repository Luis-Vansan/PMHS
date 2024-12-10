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

// Modifica para aceitar tanto POST quanto GET
if ((isset($_POST['buscado']) && $_POST['buscado'] != "") || (isset($_GET['search']) && $_GET['search'] != "")) {
    $buscado = isset($_POST['buscado']) ? $con->real_escape_string($_POST['buscado']) : $con->real_escape_string($_GET['search']);
    $q .= " AND posts.nome LIKE '%$buscado%'";
}

// Adiciona o filtro de tipo, se selecionado
if (isset($_POST['id_tipo']) && $_POST['id_tipo'] != "") {
    $id_tipo = (int)$_POST['id_tipo']; // Sanitiza o ID
    $q .= " AND post_tags.id_tag = $id_tipo";
}

// Adiciona paginação
$posts_por_pagina = 5;
$pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina_atual - 1) * $posts_por_pagina;

// Modifica a query principal para incluir LIMIT
$q .= "
    GROUP BY posts.id
    ORDER BY posts.data_post DESC
    LIMIT $posts_por_pagina OFFSET $offset
";

// Adiciona query para contar total de posts
$q_total = "
    SELECT COUNT(DISTINCT posts.id) as total 
    FROM posts 
    LEFT JOIN post_tags ON posts.id = post_tags.id_post
    LEFT JOIN tags ON post_tags.id_tag = tags.id_tag
    WHERE 1=1
";

// Adiciona os mesmos filtros à query de contagem
if ((isset($_POST['buscado']) && $_POST['buscado'] != "") || (isset($_GET['search']) && $_GET['search'] != "")) {
    $buscado = isset($_POST['buscado']) ? $con->real_escape_string($_POST['buscado']) : $con->real_escape_string($_GET['search']);
    $q_total .= " AND posts.nome LIKE '%$buscado%'";
}

if (isset($_POST['id_tipo']) && $_POST['id_tipo'] != "") {
    $id_tipo = (int)$_POST['id_tipo'];
    $q_total .= " AND post_tags.id_tag = $id_tipo";
}

// Executa a consulta
$result = $con->query($q);

// Executa a consulta de contagem
$result_total = $con->query($q_total);
$row_total = $result_total->fetch_assoc();
$total_posts = $row_total['total'];
$tem_mais_posts = ($pagina_atual * $posts_por_pagina) < $total_posts;

// Calcula o total de páginas
$total_paginas = ceil($total_posts / $posts_por_pagina);

// Consulta para listar os tipos de tags disponíveis
$query_tags = "SELECT * FROM tags";
$result_tags = $con->query($query_tags);

if (!$result) {
    die("Erro na consulta de posts: " . $con->error);
}

// Função para extrair o ID do vídeo do YouTube da URL
function getYoutubeVideoId($url) {
    preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $url, $matches);
    return isset($matches[1]) ? $matches[1] : null;
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
            <img src="images/logo.jpg" alt="PMHS Logo">
        </a>
    </header>

    <!-- Barra de busca e filtro -->
    <div class="search-container">
        <form action="" method="post" class="search-form">
            <div class="search-group">
                <input type="text" name="buscado" placeholder="Nome do exercício" value="<?= isset($_POST['buscado']) ? htmlspecialchars($_POST['buscado']) : (isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '') ?>" class="search-bar">

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
        <?php if ((isset($_POST['buscado']) && $_POST['buscado'] != "") || (isset($_GET['search']) && $_GET['search'] != "")): ?>
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
                    <h3 style="text-align: center;"><?= htmlspecialchars($row['nome']) ?></h3>
                        
                        <?php if ($row['media_type'] === 'youtube' && !empty($row['video_url'])): ?>
                            <?php $videoId = getYoutubeVideoId($row['video_url']); ?>
                            <?php if ($videoId): ?>
                                <div class="video-container" style="position: relative; padding-bottom: 30%; height: 0; overflow: hidden; margin: 0 auto 20px; max-width: 500px;">
                                    <iframe 
                                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
                                        src="https://www.youtube.com/embed/<?= $videoId ?>"
                                        frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen>
                                    </iframe>
                                </div>
                            <?php else: ?>
                                <p style="text-align: center; color: red;">Erro ao processar o vídeo do YouTube.</p>
                            <?php endif; ?>
                        <?php elseif ($row['media_type'] === 'gif' && !empty($row['gif_url'])): ?>
                            <div style="text-align: center; margin-bottom: 20px;">
                                <img src="<?= htmlspecialchars($row['gif_url']) ?>" alt="GIF" style="max-width: 500px; width: 100%;">
                            </div>
                        <?php endif; ?>

                        <p style="margin: 0 0 10px 20px; white-space: pre-wrap; word-break: break-word; text-align: justify;"><?= nl2br(htmlspecialchars($row['conteudo'])) ?></p>
                        <p style="margin: 0 0 10px 20px;"><strong>Tipo(s):</strong> <?= htmlspecialchars($row['tipos']) ?></p>
                        <p class="timestamp" style="margin: 0 0 10px 20px;">Publicado em: <?= date('d/m/Y H:i', strtotime($row['data_post'])) ?></p>
                        <p style="margin: 0 0 10px 20px;"><strong>Fonte:</strong> <a href="<?= htmlspecialchars($row['fonte']) ?>" target="_blank"><?= htmlspecialchars($row['fonte']) ?></a></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Nenhuma publicação encontrada.</p>
            <?php endif; ?>
            
            <?php if ($total_paginas > 1): ?>
                <div class="paginacao" style="text-align: center; margin: 20px 0;">
                    <?php
                    // Prepara os parâmetros base da URL
                    $params = [];
                    if (isset($_POST['buscado']) || isset($_GET['search'])) {
                        $params['search'] = isset($_POST['buscado']) ? $_POST['buscado'] : $_GET['search'];
                    }
                    if (isset($_POST['id_tipo'])) {
                        $params['id_tipo'] = $_POST['id_tipo'];
                    }

                    // Estilo para os links de paginação
                    $link_style = "display: inline-block; padding: 5px 10px; margin: 0 2px; text-decoration: none; border: 1px solid #007bff; border-radius: 3px;";
                    $active_style = $link_style . "background-color: #007bff; color: white;";
                    $inactive_style = $link_style . "background-color: white; color: #007bff;";

                    // Mostra link para primeira página se necessário
                    if ($pagina_atual > 3) {
                        $params['pagina'] = 1;
                        $url = '?' . http_build_query($params);
                        echo "<a href='$url' style='$inactive_style'>1</a>";
                        if ($pagina_atual > 4) {
                            echo "<span style='margin: 0 5px;'>...</span>";
                        }
                    }

                    // Mostra páginas ao redor da página atual
                    for ($i = max(1, $pagina_atual - 2); $i <= min($total_paginas, $pagina_atual + 2); $i++) {
                        $params['pagina'] = $i;
                        $url = '?' . http_build_query($params);
                        $style = ($i == $pagina_atual) ? $active_style : $inactive_style;
                        echo "<a href='$url' style='$style'>$i</a>";
                    }

                    // Mostra link para última página se necessário
                    if ($pagina_atual < $total_paginas - 2) {
                        if ($pagina_atual < $total_paginas - 3) {
                            echo "<span style='margin: 0 5px;'>...</span>";
                        }
                        $params['pagina'] = $total_paginas;
                        $url = '?' . http_build_query($params);
                        echo "<a href='$url' style='$inactive_style'>$total_paginas</a>";
                    }
                    ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
