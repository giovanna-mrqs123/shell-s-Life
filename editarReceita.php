<?php
session_start();
include 'conecta.php';

if(!isset($_SESSION['usuario_id'])){
    die('Você precisa estar logado para editar uma receita!');
}

$usuario_id = $_SESSION['usuario_id'];

$id = (int) $_GET['id'];
$sql = "SELECT * FROM receita WHERE id = $id AND usuario_id = $usuario_id";

$resultado = mysqli_query($conexao, $sql);

$receita = mysqli_fetch_assoc($resultado);

if (!$receita) {
    die("Receita não encontrada!");
}


//botão salvar alterações
if(isset($_POST['editar'])) {
   $nome = $_POST['nome'];
   $descricao = $_POST['descricao'];
   $imagem = $_POST['imagem'];
   $tempo = $_POST['tempo'];
   $dificuldade = $_POST['dificuldade'];
   $ingredientes = $_POST['ingredientes'];
   $modo_preparo = $_POST['modo_preparo'];

   $sqlEditar = "UPDATE receita SET 
                 nome='$nome', 
                 descricao='$descricao', 
                 imagem='$imagem', 
                 tempo='$tempo', 
                 dificuldade='$dificuldade', 
                 ingredientes='$ingredientes', 
                 modo_preparo='$modo_preparo' 
                WHERE id=$id";

mysqli_query($conexao, $sqlEditar);

   header("Location: verreceita.php?id=$id");
   exit();
}
    
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Editar receita | Shell's Life</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px 15px;
            background: linear-gradient(135deg, #f7f6e4, #e4edda);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
            width: 100%;
            max-width: 480px;
            padding: 32px;
            background-color: rgba(255, 255, 255, 0.97);
            box-shadow: 0 12px 35px rgba(53, 94, 43, 0.14);
            border: 1px solid rgba(74, 124, 58, 0.15);
            border-radius: 24px;
        }

        h2 {
            text-align: center;
            color: #355e2b;
            font-size: 28px;
            font-weight: 700;
            margin: 8px 0 28px;
            letter-spacing: -0.5px;
        }

        h2::after {
            content: "";
            display: block;
            width: 55px;
            height: 4px;
            margin: 12px auto 0;
            background-color: #8aaa72;
            border-radius: 10px;
        }

        p {
            margin: 18px 0 8px;
            font-weight: 600;
            font-size: 15px;
            color: #355e2b;
        }

        input,
        select,
        textarea {
            display: block;
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #d3dfca;
            border-radius: 12px;
            background-color: #f9fbf6;
            color: #333333;
            font-family: inherit;
            font-size: 15px;
            outline: none;
            transition: border-color 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: #4a7c3a;
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(74, 124, 58, 0.12);
        }

        textarea {
            min-height: 90px;
            line-height: 1.5;
            resize: vertical;
        }

        .imagem {
            padding: 14px;
            border: 2px dashed #a7c795;
            background-color: #f9fbf6;
            color: #355e2b;
            font-size: 13px;
            cursor: pointer;
        }

        #previaImagem {
            display: none;
            width: 100%;
            max-height: 260px;
            object-fit: cover;
            border-radius: 14px;
            margin-top: 12px;
        }

        .dica {
            margin-top: 7px;
            color: #71816a;
            font-size: 12px;
            font-weight: 400;
        }

        button {
            width: 100%;
            margin-top: 26px;
            padding: 14px;
            background-color: #4a7c3a;
            color: #ffffff;
            border: none;
            border-radius: 12px;
            font-family: inherit;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 5px 12px rgba(53, 94, 43, 0.15);
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
        }

        button:hover {
            background-color: #355e2b;
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(53, 94, 43, 0.22);
        }

        button:active {
            transform: translateY(0);
        }

        button:focus-visible {
            outline: 3px solid #a7c795;
            outline-offset: 3px;
        }

        .voltar {
            display: block;
            margin-top: 18px;
            text-align: center;
            color: #355e2b;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }

        .voltar:hover {
            color: #6b8e5a;
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            body {
                padding: 18px 12px;
            }

            .cartao {
                padding: 24px 18px;
                border-radius: 20px;
            }

            h2 {
                font-size: 25px;
            }
        }
    </style>
</head>

<body>
    <div class="cartao">
        <h2>Editar receita</h2>

        <form method="POST">

            <label>Nome da receita:</label>
            <input type="text" name="nome" value="<?php echo $receita['nome']; ?>">

            <br><br>

            <label>Descrição:</label>
            <textarea name="descricao"><?php echo $receita['descricao']; ?></textarea>

            <br><br>

            <label>Imagem:</label>
            <input type="text" name="imagem" value="<?php echo $receita['imagem']; ?>">

            <br><br>

            <label>Tempo de Preparo:</label>
            <input type="text" name="tempo" value="<?php echo $receita['tempo']; ?>">

            <br><br>

            <label>Dificuldade:</label>
            <input type="text" name="dificuldade" value="<?php echo $receita['dificuldade']; ?>">

            <br><br>

            <label>Ingredientes:</label>
            <textarea name="ingredientes"><?php echo $receita['ingredientes']; ?></textarea>

            <br><br>

            <label>Modo de Preparo:</label>
            <textarea name="modo_preparo"><?php echo $receita['modo_preparo']; ?></textarea>

            <br><br>

            <button type="submit" name="editar">Salvar alterações</button>

        </form>

        <a href="verreceita.php?id=<?php echo $receita['id']; ?>" class="voltar">Voltar para receita</a>
    </div>
</body>

</html>
