<?php
session_start();
include 'conecta.php';

if (!isset($_SESSION['usuario_id'])) {
    die("Você precisa fazer login para acessar seu caderno.");
}

$usuario_id = $_SESSION['usuario_id'];

//Recebe o ID da receita enviado pelo verreceita.php.
$receita_id = 0;

if (isset($_GET['receita_id'])) {

    $receita_id = (int) $_GET['receita_id'];

    $sqlReceita = "SELECT * FROM receita
                   WHERE id = $receita_id";

    $resultadoReceita = mysqli_query($conexao, $sqlReceita);
    //Confere se essa receita realmente existe.
    if (mysqli_num_rows($resultadoReceita) == 0) {
        die("Receita não encontrada!");
    }
}

//Guarda o ID na variável $receita_id.
if (isset($_GET['id'])) {
    $caderno_id = (int) $_GET['id'];

    $sqlVerificar = "SELECT * FROM livro_receitas
                WHERE id = $caderno_id
                AND (usuario_id = $usuario_id
                OR privacidade = 'publico')";

    $resultadoVerificar = mysqli_query($conexao, $sqlVerificar);

    if (mysqli_num_rows($resultadoVerificar) == 0) {
        die("Caderno não encontrado.");
    }
}

// Adicionar receita a um caderno existente
if (isset($_POST['adicionar'])) {

    $caderno_id = (int) $_POST['caderno_id'];

    // Verifica se o caderno pertence ao usuário
    $sqlVerificar = "SELECT * FROM livro_receitas
                     WHERE id = $caderno_id
                     AND usuario_id = $usuario_id";

    $resultadoVerificar = mysqli_query($conexao, $sqlVerificar);

    if (mysqli_num_rows($resultadoVerificar) > 0) {

        // Verifica se a receita já está salva
        $sqlJaSalva = "SELECT * FROM livro_receita
                       WHERE livro_receitas_id = $caderno_id
                       AND receita_id = $receita_id";

        $resultadoJaSalva = mysqli_query($conexao, $sqlJaSalva);

        if (mysqli_num_rows($resultadoJaSalva) == 0) {

            $sqlAdicionar = "INSERT INTO livro_receita
                             (livro_receitas_id, receita_id)
                             VALUES ($caderno_id, $receita_id)";

            mysqli_query($conexao, $sqlAdicionar);
        }

        header("Location: caderno.php?id=$caderno_id");
        exit;

    } else {
        die("Caderno inválido!");
    }
}

//criar novo caderno
if (isset($_POST['criar'])) {

    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $privacidade = $_POST['publico'];

    $sqlCriar = "INSERT INTO livro_receitas (nome, descricao, usuario_id, privacidade)
                 VALUES ('$nome', '$descricao', $usuario_id, '$privacidade')";

    if (mysqli_query($conexao, $sqlCriar)) {

        $novo_caderno_id = mysqli_insert_id($conexao);

        // Se veio de uma receita, adiciona ela ao novo caderno
        if ($receita_id > 0) {

            $sqlAdicionar = "INSERT INTO livro_receita
                             (livro_receitas_id, receita_id)
                             VALUES ($novo_caderno_id, $receita_id)";

            mysqli_query($conexao, $sqlAdicionar);

            header("Location: caderno.php?id=$novo_caderno_id");
            exit;

        } else {
            echo "Caderno criado com sucesso!";
        }

    } else {
        echo "Erro ao criar caderno: " . $conexao->error;
    }
}

//excluir caderno
if (isset($_POST['excluir'])) {
    $caderno_id = (int) $_POST['excluir'];

    $sqlVerificar = "SELECT * FROM livro_receitas
                WHERE id = $caderno_id
                AND usuario_id = $usuario_id";

    $resultadoVerificar = mysqli_query($conexao, $sqlVerificar);

    if (mysqli_num_rows($resultadoVerificar) > 0) {

        $sqlExcluirRelacoes = "DELETE FROM livro_receita
                               WHERE livro_receitas_id = $caderno_id";

        if (mysqli_query($conexao, $sqlExcluirRelacoes)) {

            $sqlExcluir = "DELETE FROM livro_receitas
                           WHERE id = $caderno_id
                           AND usuario_id = $usuario_id";

            if (mysqli_query($conexao, $sqlExcluir)) {
                header("Location: caderno.php");
                exit;
            }
        }
    }
}

$sqlCadernos = "SELECT * FROM livro_receitas
                WHERE usuario_id = $usuario_id";

$resultadoCadernos = mysqli_query($conexao, $sqlCadernos);


$sqlPublicos = "SELECT livro_receitas.*, usuario.nome AS dono
                FROM livro_receitas
                INNER JOIN usuario
                ON livro_receitas.usuario_id = usuario.id
                WHERE livro_receitas.privacidade = 'publico'
                AND livro_receitas.usuario_id != $usuario_id";

$resultadoPublicos = mysqli_query($conexao, $sqlPublicos);
if (isset($_GET['id'])) {
    $caderno_id = (int) $_GET['id'];

    $sql = "SELECT receita.*
            FROM receita
            INNER JOIN livro_receita
            ON receita.id = livro_receita.receita_id
            WHERE livro_receita.livro_receitas_id = $caderno_id";

    $resultado = mysqli_query($conexao, $sql);
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu caderno | Shell's Life</title>
    <Style>
        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background-color: rgb(247, 246, 228);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            margin: 0;
            position: relative;
            isolation: isolate;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background-image: url("download (1).png");
            background-repeat: no-repeat;
            background-position: center;
            background-size: min(75vw, 600px);
            opacity: 0.35;
            z-index: -1;
            pointer-events: none;
        }

        .cartao-formulario {
            width: 350px;
            padding: 25px;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 25px;
            display: flex;
            flex-direction: column;
        }

        h2 {
            text-align: center;
            color: #2e2e2a;
            margin-bottom: 20px;
        }

        input,
        textarea {
            width: 100%;
            box-sizing: border-box;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin-top: 8px;
            font-family: inherit;
            font-size: 14px;
        }

        textarea {
            resize: vertical;
            min-height: 90px;
        }

        button {
            background-color: #4A7C3A;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 12px 18px;
            width: 100%;
            margin-top: 15px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background-color: #365D2B;
            transform: translateY(-2px);
        }

        .voltar {
            text-decoration: none;
            color: #4a7c3a;
            font-weight: 600;
            margin-bottom: 15px;
        }


        .paginaR {
            width: 100%;
            max-width: 1000px;
            box-sizing: border-box;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
            padding: 30px;
        }

        .cartao {
            background-color: white;
            border-radius: 15px;
            padding: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            transition: transform 0.3s;
            min-width: 0;
        }

        .cartao:hover {
            transform: translateY(-5px);
        }

        .cartao h3,
        .cartao h4 {
            color: #4A7C3A;
            margin-bottom: 5px;
        }

        .cartao p {
            color: #555;
            font-size: 14px;
            line-height: 1.5;
        }

        .cartao a {
            display: block;
            background-color: #4A7C3A;
            color: white;
            text-decoration: none;
            text-align: center;
            border-radius: 8px;
            padding: 10px;
            margin-top: auto;
        }

        .cartao a:hover {
            background-color: #3f6931;
        }

        @media (max-width: 900px) {
            .paginaR {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 600px) {
            .paginaR {
                grid-template-columns: 1fr;
                padding: 15px;
            }
        }

        select {
            width: 100%;
            box-sizing: border-box;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin-top: 8px;
            font-family: inherit;
            font-size: 14px;
            background-color: white;
        }

        h1 {
            color: #3f6931;
            font-size: 32px;
            text-align: center;
            margin: 25px 0;
        }

        h2 {
            color: #4A7C3A;
            font-size: 23px;
        }

        .cartao a {
            background-color: #4a7c3a;
            color: white;
            text-decoration: none;
            text-align: center;
            border-radius: 10px;
            padding: 10px;
            transition: 0.3s;
        }

        .cartao a:hover {
            background-color: #365D2B;
        }


        .voltarInicio {
            display: block;
            text-align: center;
            margin-top: 12px;
            text-decoration: none;
            color: #4A7C3A;
            font-weight: bold;
        }

        .voltarInicio:hover {
            color: #3f6931;
        }

        .btn-excluir {
            background-color: #C85C54;
        }

        .btn-excluir:hover {
            background-color: #A9433C;
        }
    </Style>
</head>


<body>

    <?php if (!isset($_GET['id'])) { ?>

        <h1>Meu caderno de Receitas</h1>
        <?php
        if ($receita_id > 0 && mysqli_num_rows($resultadoCadernos) > 0) {
            $resultadoEscolha = mysqli_query($conexao, $sqlCadernos);
            ?>

            <div class="cartao-formulario">
                <h2>Adicionar receita ao caderno</h2>

                <form method="POST">

                    <label>Escolha um caderno:</label>

                    <select name="caderno_id" required>
                        <?php
                        while ($caderno = mysqli_fetch_assoc($resultadoEscolha)) {
                            ?>
                            <option value="<?php echo $caderno['id']; ?>">
                                <?php echo $caderno['nome']; ?>
                            </option>
                            <?php
                        }
                        ?>
                    </select>

                    <button type="submit" name="adicionar">
                        Adicionar receita
                    </button>

                </form>
            </div>

        <?php } ?>

        <div class="cartao-formulario">
            <h2>Criar novo caderno</h2>

            <form method="POST">
                <input type="text" name="nome" placeholder="Nome do caderno" required>

                <select name="publico" required>
                    <option value="publico">Público</option>
                    <option value="privado">Privado</option>
                </select>

                <textarea name="descricao" placeholder="Descrição do caderno"></textarea>

                <button type="submit" name="criar">Criar caderno</button>

                <header>
                    <a href="index.php" class="voltarInicio">Voltar para o início</a>
                </header>
            </form>
        </div>

        <div class="paginaR">
            <?php
            while ($caderno = mysqli_fetch_assoc($resultadoCadernos)) {
                ?>
                <div class="cartao">
                    <h3><?php echo $caderno['nome']; ?></h3>
                    <p><?php echo $caderno['descricao']; ?></p>

                    <a href="caderno.php?id=<?php echo $caderno['id']; ?>">Abrir caderno</a>

                    <form method="POST">
                        <button class="btn-excluir" type="submit" name="excluir"
                            value="<?php echo $caderno['id']; ?>">Excluir</button>
                    </form>
                </div>
            <?php } ?>
        </div>
        <h2>Cadernos Públicos</h2>
        <div class="paginaR">
            <?php
            while ($publico = mysqli_fetch_assoc($resultadoPublicos)) {
                ?>
                <div class="cartao">
                    <h3><?php echo $publico['nome']; ?></h3>
                    <p><?php echo $publico['descricao']; ?></p>
                    <p>Criado por: <?php echo $publico['dono']; ?></p>
                    <a href="caderno.php?id=<?php echo $publico['id']; ?>">Abrir caderno</a>
                </div>
            <?php } ?>
        </div>
    <?php } ?>

    <?php
    if (isset($_GET['id'])) {
        ?>
        <h1>Receitas do caderno</h1>

        <a href="caderno.php" class="voltar">Voltar para meus cadernos</a>

        <div class="paginaR">
            <?php
            if (mysqli_num_rows($resultado) > 0) {
                while ($receita = mysqli_fetch_assoc($resultado)) {
                    ?>
                    <div class="cartao">
                        <h4><?php echo $receita['nome']; ?></h4>
                        <p><?php echo $receita['descricao']; ?></p>
                        <a href="verreceita.php?id=<?php echo $receita['id']; ?>">Ver receita</a>
                    </div>
                    <?php
                }
            } else {
                echo "Seu caderno ainda está vazio!";
            }
            ?>
        </div>
        <?php
    }
    ?>

</body>

</html>
