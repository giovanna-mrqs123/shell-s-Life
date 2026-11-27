<?php
session_start();
include 'conecta.php';

if (isset($_POST['curtir'])) {
    if (!isset($_SESSION['usuario_id'])) {
        die("Você precisa estar logado para curtir uma receita!");
    }

    $usuario_id = $_SESSION['usuario_id'];
    $receita_id = (int) $_POST['curtir'];

    $sqlVerificar = "SELECT * FROM interacao
                     WHERE usuario_id = $usuario_id
                     AND receita_id = $receita_id
                     AND tipo = 'curtida'";

    $resultadoVerificar = mysqli_query($conexao, $sqlVerificar);

    if (mysqli_num_rows($resultadoVerificar) > 0) {

        $sqlCurtir = "DELETE FROM interacao
                      WHERE usuario_id = $usuario_id
                      AND receita_id = $receita_id
                      AND tipo = 'curtida'";

    } else {

        $sqlCurtir = "INSERT INTO interacao
                      (tipo, nota, data_interacao, usuario_id, receita_id)
                      VALUES ('curtida', NULL, NOW(), $usuario_id, $receita_id)";
    }

    mysqli_query($conexao, $sqlCurtir);

    header("Location: index.php");
    exit;
}

// Verifica se existe uma mensagem de exclusão
if (isset($_SESSION['mensagem_exclusao'])) {
    echo "<p>" . $_SESSION['mensagem_exclusao'] . "</p>";

    unset($_SESSION['mensagem_exclusao']);
}

// Busca todas as receitas
$sql = "SELECT * FROM receita";
$resultado = mysqli_query($conexao, $sql);
$resProdutos = mysqli_query($conexao, "SELECT * FROM produto ORDER BY id DESC");
$ehAdmin = isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'admin';
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shell's Life</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    <style>
        /* guardam as cores para reutilizar em várias partes do site. */

        :root {
            --verde-claro: #A7D489;
            --verde-escuro: #4A7C3A;
            --creme: #F7F6E4;
            --dourado: #C4B477;
            --branco: #FFFFFF;
        }

        body {
            background-color: var(--creme);
        }

        .bg-body-tertiary {
            --bs-bg-opacity: 1;
            background-color: var(--verde-claro) !important;
        }

        .navbar-brand {
            width: 75px;
            height: 75px;
            object-fit: contain;
            padding: 4px;
            margin-right: 12px;
        }

        .container-fluid {
            border-bottom: 1px solid rgba(74, 124, 58, 0.25);
            min-height: 90px;
            padding: 8px 24px;
        }

        .menuP {
            position: fixed;
            top: 21px;
            right: 30px;
            background-color: green;
            height: 40px;
            width: 70px;
            border-radius: 20%;
        }

        .nav-linkactive {
            font-size: 28px;
            color: var(--verde-escuro);
            text-decoration: none;
            font-weight: 700;
            font-family: 'Segoe UI', sans-serif;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
        }

        .navbar-collapse {
            align-items: center;
        }

        .navbar-nav {
            gap: 12px;
        }

        .navbar-nav .nav-link {
            padding: 8px 10px;
        }

        .navbar-nav .nav-link {
            color: var(--verde-escuro);
            font-size: 16px;
            font-weight: 500;
            margin: 0 8px;
            transition: color 0.2s ease;
        }

        .navbar-nav .nav-link {
            color: var(--verde-escuro);
            text-decoration: none;
            font-size: 16px;
            font-weight: 600;
            margin: 0 8px;
            padding: 10px 14px;
            border-radius: 10px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.45);
            color: #355e2b;
        }

        .navbar-nav .nav-link:focus-visible {
            outline: 2px solid var(--verde-escuro);
            outline-offset: 3px;
        }

        .btn-success {
            background-color: var(--verde-escuro);
            border-color: var(--verde-escuro);
        }

        .btn-success:hover {
            background-color: #355e2b;
            border-color: #355e2b;
        }

        .btn-outline-success {
            color: var(--verde-escuro);
            border-color: var(--verde-escuro);
        }

        .btn-outline-success:hover {
            background-color: var(--verde-escuro);
            color: var(--branco);
        }

        .paginaR {
            max-width: 1000px;
            margin: 30px auto;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 22px;
        }

        @media (max-width: 900px) {
            .paginaR {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 600px) {
            .paginaR {
                grid-template-columns: repeat(1, 1fr);
            }
        }

        .Tela {
            max-width: 1240px;
            margin: 0 auto;
            padding: 0 16px;
        }

        @media (max-width: 600px) {
            .paginaR {
                grid-template-columns: repeat(1, 1fr);
            }
        }

        .receita img {
            width: 100%;
            height: 210px;
            object-fit: cover;
            display: block;
            transition: transform 0.4s ease;
        }

        .receita:hover img {
            transform: scale(1.04);
        }

        .corpoR h4 {
            color: var(--verde-escuro);
            font-size: 22px;
            font-weight: bold;
            margin: 15px 15px 10px;
            font-family: 'Segoe UI', sans-serif;
        }

        .corpoR {
            padding: 18px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .corpoR .btn {
            align-self: flex-start;
        }

        .desc {
            color: #5f665a;
            font-size: 14px;
            line-height: 1.6;
            flex-grow: 1;
            margin-bottom: 15px;
        }

        .tempdifi {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
            margin-bottom: 0;
        }

        .tempdifi .badge {
            padding: 7px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .tempdifi .bg-primary {
            background-color: var(--dourado) !important;
            color: #FFFFFF !important;
        }

        .tempdifi .bg-success {
            background-color: var(--verde-claro) !important;
            color: var(--verde-escuro) !important;
        }

        .acoesReceita {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-top: auto;
            flex-wrap: wrap;
        }

        .acoesReceita .btn {
            margin-top: 0 !important;
            white-space: nowrap;
        }

        .cartao {
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(74, 124, 58, 0.12);
            background-color: var(--branco);
            display: flex;
            flex-direction: column;
            height: 100%;
            border: 1px solid rgba(167, 212, 137, 0.5);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .cartao:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(74, 124, 58, 0.2);
        }

        .btn-outline-success {
            height: 40px;
        }

        .btn-success {
            margin-right: 10px;
        }

        html {
            scroll-behavior: smooth;
        }

        #receitas {
            scroll-margin-top: 20px;
        }

        .btn-curtir {
            background-color: white;
            border: 0;
            font-size: 20px;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .btn-curtir.curtido {
            background-color: green;
            color: white;
        }
    </style>
</head>

<body>
    <div class="offcanvas offcanvas-end" tabindex="-1" id="menuLateral" aria-labelledby="menuLateralLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="menuLateralLabel">Menu</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body d-flex flex-column gap-2">
            <a href="index.php" class="btn btn-outline-success text-start">Início</a>
            <a href="receita.php" class="btn btn-outline-success text-start" id="linkPublicar"
                onclick="verificarLogin(event)">Publicar receita</a>
            <a href="caderno.php" class="btn btn-outline-success text-start">Meu caderno de receitas</a>
            <a href="minhaConta.php" class="btn btn-outline-success text-start">Minha conta</a>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <img src="download (1).png" alt="logo Shell's Life" class="navbar-brand">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <a class="nav-linkactive me-4" aria-current="page" href="index.php">Shell's Life</a>

                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Início</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="irParaReceitas(event)">Receitas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#produtos">Produtos</a>
                    </li>
                </ul>

                <form class="d-flex" role="search">
                    <a href="login.php" id="btnLogar" class="btn btn-success">Login</a>
                    <button id="menu" class="btn btn-outline-success" type="button" data-bs-toggle="offcanvas"
                        data-bs-target="#menuLateral" aria-controls="menuLateral">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-list" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <section id="receitas">
        <div class="paginaR">
            <?php
            while ($receita = mysqli_fetch_assoc($resultado)) {


                $receita_id = $receita['id'];
                $curtida = false;

                if (isset($_SESSION['usuario_id'])) {
                    $usuario_id = $_SESSION['usuario_id'];

                    $sqlCurtida = "SELECT * FROM interacao
                       WHERE usuario_id = $usuario_id
                       AND receita_id = $receita_id
                       AND tipo = 'curtida'";

                    $resultadoCurtida = mysqli_query($conexao, $sqlCurtida);

                    if (mysqli_num_rows($resultadoCurtida) > 0) {
                        $curtida = true;
                    }
                }
                ?>
                <div class="cartao">
                    <div class="receita">
                        <img src="<?php echo $receita['imagem']; ?>" alt="">
                    </div>

                    <div class="corpoR">
                        <h4><?php echo $receita['nome']; ?></h4>
                        <p class="desc"><?php echo $receita['descricao']; ?></p>
                        <div class="acoesReceita">
                            <a href="verreceita.php?id=<?php echo $receita['id']; ?>" class="btn btn-success">Ver
                                Receita</a>
                            <form method="POST">
                                <button class="btn btn-success" type="submit" name="curtir"
                                    value="<?php echo $receita['id']; ?>">
                                    <?php
                                    if ($curtida == true) {
                                        echo "♥";
                                    } else {
                                        echo "♡";
                                    }
                                    ?>
                                </button>
                            </form>
                            <div class="tempdifi">
                                <span class="badge bg-primary">
                                    <?php echo $receita['tempo']; ?> min
                                </span>
                                <span class="badge bg-success">
                                    <?php echo $receita['dificuldade']; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>



        <div class="modal fade" id="modalSaladaFrango" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Salada com Frango</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <img src="https://p2.trrsf.com/image/fget/cf/1200/1200/middle/images.terra.com/2022/02/14/233248378-receitas-saudaveis-faceis-deliciosas-jantar.jpg"
                            class="img-fluid rounded mb-3" alt="">
                        <p><strong>Ingredientes:</strong> peito de frango, alface, tomate, pepino, grão-de-bico,
                            abacate...</p>
                        <p><strong>Modo de preparo:</strong> grelhe o frango, corte os vegetais, monte a salada e
                            finalize com o molho de orégano.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <section id="produtos" class="container py-5">
        <h2 class="text-center mb-4" style="color: #4A7C3A;">Nossos Produtos</h2>

        <p class="text-center mb-4">
            Conheça produtos que podem fazer parte de uma alimentação variada.
        </p>

        <?php if ($ehAdmin) { ?>
            <div class="text-center">
                <a href="produto.php" class="btn btn-success" style="margin-right: 0;">Adicionar produto</a>
            </div>
        <?php } ?>

        <div class="paginaR">
            <?php while ($produto = mysqli_fetch_assoc($resProdutos)) { ?>
                <div class="cartao">
                    <div class="receita">
                        <img src="<?php echo htmlspecialchars($produto['imagem']); ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>">
                    </div>

                    <div class="corpoR">
                        <h4><?php echo htmlspecialchars($produto['nome']); ?></h4>
                        <p class="desc"><?php echo htmlspecialchars($produto['descricao']); ?></p>
                    </div>

                    </div>
                </div>
            <?php } ?>  
    </section>

    <script>
        function verificarLogin(event) {
            if (<?php echo isset($_SESSION['usuario_id']) ? 'true' : 'false'; ?> == false) {
                event.preventDefault();
                alert('Você precisa estar logado para publicar uma receita!');
                window.location.href = 'login.php';
            }
        }

        if (<?php echo isset($_SESSION['usuario_id']) ? 'true' : 'false'; ?>) {
            document.getElementById("btnLogar").style.display = "none";
        }
    </script>
</body>

</html>
