<?php
 session_start();
 include 'conexao.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PMHS</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">

</head>
<body>
    
<!-- header section starts  -->

<header class="header">

    <a href="#" class="logo">
        <img src="images/logo.jpg" alt="">
    </a>

    <nav class="navbar">
        <a href="#home">HOME</a>
        <a href="#blogs">EXERCÍCIOS</a>
        <a href="#about">SOBRE</a>
        <a href="#review">DEVS</a>
        <a href="#contact">CONTATO</a>
    </nav>

    <div class="icons">
        <div class="fas fa-search" id="search-btn"></div>
        <div class="fas fa-user" id="cart-btn"></div>
        <div class="fas fa-bars" id="menu-btn"></div>
    </div>

    
    <form action="feed.php" method="get" class="search-form">
        <input type="search" name="search" id="search-box" placeholder="search here...">
        <button type="submit" class="search-button fas fa-search"></button>
    </form>
    

    <div class="cart-items-container">
        <?php if (isset($_SESSION['usuario_id'])): ?> <!-- Verifica se o usuário está logado -->
            <!-- Exibe o nome do usuário -->
            <strong><span style="font-size: 20px;" class="cart-item">Olá, <?php echo htmlspecialchars($_SESSION['nome']); ?>!</span></strong>
            <div class="profile-option">
                <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1): ?> <!-- Verifica se o usuário é administrador -->
                    <a href="feedadm.php" class="btn">Publicar</a>
                <?php endif; ?>
                <a href="logout.php" class="btn">Sair</a>
            </div>
        <?php else: ?>
            <div class="profile-option">
                <a href="login.php" class="btn">Logar</a>
            </div>
        <?php endif; ?>
    </div>


</header>

<!-- header section ends -->

<!-- home section starts  -->

<section class="home" id="home">

    <div class="content">
        <h3>Saúde Física e mental nos esportes</h3>
        <p>Melhore seu desempenho esportivo gradualmente utilizando o PMHS</p>
        <a href="#blogs" class="btn">Comece agora</a>
    </div>

</section>

<!-- home section ends -->


<!-- blogs section starts  -->

<section class="blogs" id="blogs">
    <h1 class="heading">Artigos <span>/ Exercícios</span></h1>
    <div class="box-container">
        <?php
        // Query para obter as 3 publicações mais recentes, incluindo o link da fonte
        $sql = "SELECT nome, conteudo, data_post, imagem_url, fonte FROM posts ORDER BY data_post DESC LIMIT 3";
        $result = $con->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $image_url = $row['imagem_url'] ? htmlspecialchars($row['imagem_url']) : 'images/default-blog.jpg';
                $fonte_url = $row['fonte'] ? htmlspecialchars($row['fonte']) : '#'; // Link da fonte ou fallback se não houver

                echo '
                <div class="box">
                    <div class="image">
                        <img src="' . $image_url . '" alt="Blog Image">
                    </div>
                    <div class="content">
                        <a href="#" class="title">' . htmlspecialchars($row['nome']) . '</a>
                        <span>' . date("d M, Y", strtotime($row['data_post'])) . '</span>
                        <p>' . substr(htmlspecialchars($row['conteudo']), 0, 100) . '...</p>
                        <a href="feed.php?search=' . urlencode($row['nome']) . '" class="btn">Leia mais</a>
                    </div>
                </div>';
            }
        } else {
            echo '<p>No recent posts available.</p>';
        }

        // Fecha a conexão com o banco de dados
        $con->close();
        ?>
    </div>
</section>



<!-- blogs section ends -->

<!-- about section starts  -->

<section class="about" id="about">

    <h1 class="heading"> <span>Sobre</span> nós </h1>

    <div class="row">

        <div class="image">
            <img src="images/about-us.png" alt="">
        </div>

        <div class="content">
            <h3></h3>
            <p>No PMHS (Práticas para Melhorar a Saúde), acreditamos que o equilíbrio entre saúde física e mental é essencial para o desempenho esportivo. Nosso site oferece informações confiáveis, baseadas em estudos científicos, para ajudar praticantes de esportes e atividades físicas a evitar lesões, melhorar o rendimento e enfrentar desafios emocionais, como a ansiedade durante competições.</p>
            <p>Inspirados por nossas experiências em esportes como futsal, basquete e vôlei, reunimos dicas práticas e orientações psicológicas para promover um desempenho seguro e eficiente. Valorizamos a ciência, a acessibilidade e o bem-estar integral, sendo uma fonte confiável para quem busca transformar o cuidado com a saúde em um diferencial positivo e alcançar o seu melhor.</p>            
            <a href="#" class="btn">learn more</a>
        </div>

    </div>

</section>

<!-- about section ends -->



<!-- review section starts  -->

<section class="review" id="review">
    <h1 class="heading">PMHS <span>Desenvolvedores</span></h1>
    <div class="box-container">

        <!-- Desenvolvedor 1 -->
        <div class="box">
            <img src="images/LuisVansan.jpeg" class="user" alt="Foto de Luis Vansan">
            <h3>Luis Vansan</h3>
            <p><strong>Cargo:</strong> Desenvolvedor Back-end</p>
            <p>Luis é um estudante dedicado do IFSP-CMP, com uma forte paixão por desenvolvimento de soluções back-end. Ele possui habilidades em PHP, JavaScript e frameworks, e está sempre em busca de aprender novas tecnologias para melhorar suas habilidades.</p>
            <p><a style="color: #1abc9c" href="https://www.instagram.com/luis.vansan.5/" target="_blank">Instagram</a> | <a style="color: #1abc9c" href="https://github.com/Luis-Vansan" target="_blank">GitHub</a></p>
        </div>

        <!-- Desenvolvedor 2 -->
        <div class="box">
            <img src="images/PedroMigas.png" class="user" alt="Foto de Pedro Miguel">
            <h3>Pedro Miguel</h3>
            <p><strong>Cargo:</strong> Desenvolvedor Front-end</p>
            <p>Pedro é um estudante do IFSP-CMP com um forte foco em desenvolvimento front-end. Ele se destaca na criação de interfaces de usuário intuitivas e responsivas, utilizando tecnologias como HTML, CSS e JavaScript. Ele está sempre buscando aprimorar suas habilidades e contribuir para projetos desafiadores.</p>
            <p><a style="color: #1abc9c" href="https://www.instagram.com/pedro_sarur/" target="_blank">Instagram</a> | <a style="color: #1abc9c" href="https://github.com/Pedro-Miguel-S" target="_blank">GitHub</a></p>
        </div>

        <!-- Desenvolvedor 3 -->
        <div class="box">
            <img src="images/Matheus.jpeg" class="user" alt="Foto de Matheus Santos">
            <h3>Matheus Santos</h3>
            <p><strong>Cargo:</strong> Analista de Dados</p>
            <p>Matheus é um estudante do IFSP-CMP com experiência em análise de dados e manipulação de grandes volumes de informação. Ele possui um grande interesse em encontrar insights valiosos e aplicar soluções analíticas para apoiar a tomada de decisões. Com habilidades em ferramentas de análise de dados, Matheus busca contribuir para a eficiência dos projetos do time.</p>
            <p><a style="color: #1abc9c" href="https://www.instagram.com/_matsans/" target="_blank">Instagram</a> | <a style="color: #1abc9c" href="https://github.com/Mat800yy" target="_blank">GitHub</a></p>
        </div>

    </div>
</section>


<!-- review section ends -->

<!-- contact section starts  -->

<section class="contact" id="contact">

    <h1 class="heading"> <span>Contacte</span>-nos </h1>

    <div class="row">

        <iframe class="map" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3674.063210044794!2d-47.150870528024136!3d-22.94789908773501!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94c8c75cc7206185%3A0x2c426eb9f4786277!2sInstituto%20Federal%20de%20Educa%C3%A7%C3%A3o%2C%20Ci%C3%AAncia%20e%20Tecnologia%20de%20S%C3%A3o%20Paulo%20-%20IFSP%20C%C3%A2mpus%20Campinas!5e0!3m2!1spt-BR!2sbr!4v1733359376327!5m2!1spt-BR!2sbr" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            <form action="">
            <h3>entre em contato</h3>
            <div class="inputBox">
                <span class="fas fa-user"></span>
                <input type="text" placeholder="name">
            </div>
            <div class="inputBox">
                <span class="fas fa-envelope"></span>
                <input type="email" placeholder="email">
            </div>
            <div class="inputBox">
                <span class="fas fa-phone"></span>
                <input type="number" placeholder="number">
            </div>
            <input type="submit" value="contact now" class="btn">
        </form>

    </div>

</section>

<!-- contact section ends -->



<!-- footer section starts  -->

<section class="footer">

    <div class="share">
        <a href="#" class="fab fa-facebook-f"></a>
        <a href="#" class="fab fa-instagram"></a>
    </div>

    <div class="links">
        <a href="#">home</a>
        <a href="#about">sobre</a>
        <a href="#review">devs</a>
        <a href="#contact">contato</a>
        <a href="#blogs">exercícios</a>
    </div>

    <div class="credit">created by <span>PMHS Developers</span> | all rights reserved</div>

</section>

<!-- footer section ends -->

















<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>