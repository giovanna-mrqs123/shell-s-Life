<?php
session_start();
include 'conecta.php';


//salvar avaliações da receita
$id = $_GET['id'];

// Salvar avaliação da receita
if (isset($_POST['avaliar'])) {

    if (!isset($_SESSION['usuario_id'])) {
        die("Você precisa estar logado para avaliar uma receita!");
    }

    $usuario_id = $_SESSION['usuario_id'];
    $nota = (int) $_POST['nota'];

    if ($nota >= 1 && $nota <= 5) {

        $sqlVerificar = "SELECT * FROM interacao
                         WHERE usuario_id = $usuario_id
                         AND receita_id = $id
                         AND tipo = 'avaliacao'";

        $resultadoVerificar = mysqli_query($conexao, $sqlVerificar);

        if (mysqli_num_rows($resultadoVerificar) > 0) {

            $sqlAvaliar = "UPDATE interacao
                           SET nota = $nota, data_interacao = NOW()
                           WHERE usuario_id = $usuario_id
                           AND receita_id = $id
                           AND tipo = 'avaliacao'";

        } else {

            $sqlAvaliar = "INSERT INTO interacao
                           (tipo, nota, data_interacao, usuario_id, receita_id)
                           VALUES ('avaliacao', $nota, NOW(), $usuario_id, $id)";
        }

        mysqli_query($conexao, $sqlAvaliar);

        header("Location: verreceita.php?id=$id");
        exit;
    }
}
$sql = "SELECT * FROM receita WHERE id = $id";
$resultado = mysqli_query($conexao, $sql);

$receita = mysqli_fetch_array($resultado);

if (!$receita) {
    die("Receita não encontrada!");
}

//confirma a exclusao da receita
if (isset($_POST['excluir'])) {
    //tem usuario logado?
    if (!isset($_SESSION['usuario_id'])) {
        die("Você precisa estar logado para excluir uma receita!");
    }
    $usuario_id = $_SESSION['usuario_id'];

    //pertence ao usuario logado?
    if ($receita['usuario_id'] != $usuario_id) {
        die("Você não tem permissão para excluir essa receita!");
    }

    //excluir interacoes
    $sqlInteracao = "DELETE FROM interacao WHERE receita_id = $id";
    mysqli_query($conexao, $sqlInteracao);

    //exluir receita do caderno
    $sqlCaderno = "DELETE FROM livro_receita WHERE receita_id = $id";
    mysqli_query($conexao, $sqlCaderno);

    //excluir receita
    $sqlExcluir = "DELETE FROM receita WHERE id = $id";
    mysqli_query($conexao, $sqlExcluir);

    $_SESSION['mensagem'] = "Receita excluída com sucesso!";
    header("Location: index.php");
    exit;
}

$mostrarCadernos = false;

//descobrir se o usuário já avaliou aquela receita
$notaUsuario = 0;

if (isset($_SESSION['usuario_id'])) {

    $usuario_id = $_SESSION['usuario_id'];

    $sqlNota = "SELECT nota FROM interacao
                WHERE usuario_id = $usuario_id
                AND receita_id = $id
                AND tipo = 'avaliacao'";

    $resultadoNota = mysqli_query($conexao, $sqlNota);

    if (mysqli_num_rows($resultadoNota) > 0) {
        $avaliacao = mysqli_fetch_assoc($resultadoNota);
        $notaUsuario = $avaliacao['nota'];
    }
}

//identifica o usuario, busca seus cadernos e verifica quantos ele tem
if (isset($_SESSION['usuario_id'])) {
    $usuario_id = $_SESSION['usuario_id'];
    $sqlCadernos = "SELECT * FROM livro_receitas 
                    WHERE usuario_id = $usuario_id";

    $resultadosCadernos = mysqli_query($conexao, $sqlCadernos);
    $totalCadernos = mysqli_num_rows($resultadosCadernos);
}

// Quando o usuário escolhe um caderno
if (isset($_POST['escolher'])) {

    $caderno_id = (int) $_POST['caderno_id'];

    if (!isset($_SESSION['usuario_id'])) {
        die("Você precisa estar logado para salvar uma receita!");
    }

    $usuario_id = $_SESSION['usuario_id'];

    // Verifica se o caderno pertence ao usuário
    $sqlVerifica = "SELECT * FROM livro_receitas
                            WHERE id = $caderno_id
                            AND usuario_id = $usuario_id";

    $resultadoVerifica = mysqli_query($conexao, $sqlVerifica);

    if (mysqli_num_rows($resultadoVerifica) > 0) {

        // Verifica se a receita já está salva
        $sqlJaSalva = "SELECT * FROM livro_receita
                            WHERE livro_receitas_id = $caderno_id
                            AND receita_id = $id";

        $resultadoJaSalva = mysqli_query($conexao, $sqlJaSalva);

        if (mysqli_num_rows($resultadoJaSalva) == 0) {

            $sqlSalvar = "INSERT INTO livro_receita
                                (livro_receitas_id, receita_id)
                                VALUES ($caderno_id, $id)";

            mysqli_query($conexao, $sqlSalvar);

            echo "Receita adicionada ao caderno!";

        } else {
            echo "Essa receita já está nesse caderno!";
        }

    } else {
        echo "Caderno inválido!";
    }
}


// Quando o usuário clica em adicionar ao caderno
if (isset($_POST['salvar'])) {

    //verifica se o usuário está logado
    if (!isset($_SESSION['usuario_id'])) {
        die("Você precisa estar logado para salvar uma receita!");
    } else {
        // Se não tiver nenhum caderno
        if ($totalCadernos == 0) {
            echo "Você ainda não possui cadernos. Crie um para salvar a receita.";
        }

        // Se tiver apenas um caderno
        else if ($totalCadernos == 1) {
            $caderno = mysqli_fetch_assoc($resultadosCadernos);
            $caderno_id = $caderno['id'];

            $sqlJaSalva = "SELECT * FROM livro_receita
                WHERE livro_receitas_id = $caderno_id
                AND receita_id = $id";

            $resultadoJaSalva = mysqli_query($conexao, $sqlJaSalva);

            if (mysqli_num_rows($resultadoJaSalva) == 0) {

                $sqlSalvar = "INSERT INTO livro_receita
                    (livro_receitas_id, receita_id)
                    VALUES ($caderno_id, $id)";

                mysqli_query($conexao, $sqlSalvar);

                echo "Receita adicionada ao seu caderno!";

            } else {

                echo "Essa receita já está nesse caderno!";

            }

        }

        // Se tiver mais de um caderno
        else if ($totalCadernos > 1) {

            $mostrarCadernos = true;

        }

    }
}

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver receita | Shell's Life</title>

    <style>
        body {
            display: flex;
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
            background-size: min(85vw, 700px);
            opacity: 0.35;
            z-index: -1;
            pointer-events: none;
        }

        .cartao {
            width: 350px;
            padding: 15px;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10%;
            display: flex;
            flex-direction: column;
            justify-content: start;
            align-items: center;
        }

        .cartao img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            border-radius: 12px;
        }

        h2 {
            text-align: center;
            color: #2e2e2a;
            margin: 16px 0 4px;
        }

        p {
            margin: 8px 0;
            font-size: 16px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            width: 100%;
        }

        .voltar {
            align-self: flex-start;
            text-decoration: none;
            color: #4a7c3a;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .voltar:hover {
            color: #3f6931;
        }

        .tempdifi {
            display: flex;
            gap: 8px;
            margin: 6px 0;
        }

        .badge {
            padding: 5px 10px;
            border-radius: 8px;
            color: white;
            font-size: 14px;
        }

        .badge.tempo {
            background-color: #0d6efd;
        }

        .badge.dificuldade {
            background-color: #198754;
        }

        .titulo-secao {
            font-weight: 700;
            margin-top: 14px;
            margin-bottom: 2px;
        }

        button {
            margin-top: 20px;
            background-color: #4a7c3a;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 10px;
            width: 100%;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
        }

        button:hover {
            background-color: #3f6931;
        }

        ul,
        ol {
            width: 100%;
            padding-left: 22px;
            margin: 6px 0;
        }

        li {
            margin-bottom: 6px;
            font-size: 16px;
        }

        .adicionar {
            width: auto;
            min-width: 350px;
            padding: 10px 20px;
            align-self: center;
        }

        .excluir {
            width: auto;
            min-width: 350px;
            padding: 10px 20px;
            align-self: center;
        }

        .form-avaliacao {
            width: 100%;
            text-align: center;
        }

        .estrelas {
             display: flex;
             flex-direction: row-reverse;
             justify-content: center;
             gap: 5px;
             margin: 15px 0;
        }

        .estrelas input {
            display: none;
        }

            
        .estrelas label {
            font-size: 35px;
            color: #ccc;
            cursor: pointer;
        }

        .estrelas label:hover,
        .estrelas label:hover ~ label,
        .estrelas input:checked ~ label {
            color: #C4B477;
        }

        .titulo-avaliacao {
            text-align: center;
            margin-bottom: 5px;
        }

        .estrelas label {
            font-size: 30px;
        }
    </style>
</head>

<body>

    <div class="cartao">
        <a href="index.php" class="voltar">Voltar</a>
        <img src="<?php echo $receita['imagem']; ?>" alt="<?php echo $receita['nome']; ?>">
        <h2><?php echo $receita['nome']; ?></h2>
        <p><?php echo $receita['descricao']; ?></p>

        <div class="tempdifi">
            <span class="badge tempo">
                <?php echo $receita['tempo']; ?> min
            </span>

            <span class="badge dificuldade">
                <?php echo $receita['dificuldade']; ?>
            </span>
        </div>
        <p class="titulo-secao">Ingredientes</p>

        <ul>
            <?php
            $ingredientes = explode("\n", $receita['ingredientes']);
            foreach ($ingredientes as $item) {
                echo "<li>$item</li>";
            }
            ?>
        </ul>

        <p class="titulo-secao">Modo de Preparo</p>
        <ol>
            <?php
            $modo = explode("\n", $receita['modo_preparo']);
            foreach ($modo as $item) {
                echo "<li>$item</li>";
            }
            ?>
        </ol>

<p class="titulo-secao titulo-avaliacao">Avalie esta receita</p>

<form method="POST" class="form-avaliacao">

    <div class="estrelas">
        <?php
        for ($i = 1; $i <= 5; $i++) {
            ?>
            <!-- rádio: cria as opçoes de nota -->
            <input type="radio" name="nota"
                value="<?php echo $i; ?>"
                id="estrela<?php echo $i; ?>"
                <?php if ($notaUsuario == $i) echo "checked"; ?>
                required>

            <label for="estrela<?php echo $i; ?>">★</label>
            <?php
        }
        ?>
    </div>

    <button type="submit" name="avaliar">
        Enviar avaliação
    </button>

</form>

        <button type="button"
            onclick="window.location.href='editarReceita.php?id=<?php echo $receita['id']; ?>'">Editar</button>
        <form method="POST" onsubmit="return confirm('Tem certeza que deseja excluir essa receita?')">
            <button type="submit" name="excluir" class="excluir">Excluir</button>
        </form>
        <form method="POST">
            <button type="button" class="adicionar"
              onclick="window.location.href='caderno.php?receita_id=<?php echo $id; ?>'">Adicionar ao caderno
            </button>
        </form>
        <?php
        if ($mostrarCadernos) {
            ?>

            <form method="POST">

                <p>Escolha em qual caderno deseja salvar:</p>

                <select name="caderno_id" required>

                    <?php
                    $sqlLista = "SELECT * FROM livro_receitas
                                WHERE usuario_id = $usuario_id";

                    $resultadoLista = mysqli_query($conexao, $sqlLista);

                    while ($caderno = mysqli_fetch_assoc($resultadoLista)) {
                        ?>

                        <option value="<?php echo $caderno['id']; ?>">
                            <?php echo $caderno['nome']; ?>
                        </option>

                        <?php
                    }
                    ?>

                </select>

                <button type="submit" name="escolher">
                    Salvar receita
                </button>

            </form>

            <?php
        }
        ?>
    </div>
</body>

</html>
