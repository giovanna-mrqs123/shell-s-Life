<?php
session_start();
include 'conecta.php';

//verifica se o usuario esta logado
if(!isset($_SESSION['usuario_id'])){
    header("Location: login.php");
    exit;
}

//verifica se o form foi enviado
if(isset($_POST['nome'])){

   //pega o id
   $usuario_id = $_SESSION['usuario_id'];

   //recebe os dados
   $nome = mysqli_real_escape_string($conexao, $_POST['nome']);
   $descricao = mysqli_real_escape_string($conexao, $_POST['descricao']);
   $tempo = (int) $_POST['tempo'];
   $dificuldade = mysqli_real_escape_string($conexao, $_POST['dificuldade']);
   $ingredientes = mysqli_real_escape_string($conexao, $_POST['ingredientes']);
   $modo_preparo = mysqli_real_escape_string($conexao, $_POST['modo_preparo']);
   $privacidade = mysqli_real_escape_string($conexao, $_POST['privacidade']);
   //rcebe a imagem
   $imagem = $_FILES['imagem'];

   // Verifica se a imagem foi enviada corretamente e se é uma imagem válida
    if ($imagem['error'] == 0 && getimagesize($imagem['tmp_name'])) {
        
        //cria um nome unico para evitar que as imagens tenham o mesmo nome
        $nomeImagem = uniqid() . "_" . basename($imagem['name']);

        //define onde a imagem será salva
        $caminhoImagem = "uploads/" . $nomeImagem;
        
        //move a imagem para a pasta uploads
        if(move_uploaded_file($imagem['tmp_name'] , $caminhoImagem)){
            //insere os dodos da receita no banco
            $sql = "INSERT INTO receita
            (nome, descricao, tempo, dificuldade, imagem, ingredientes, modo_preparo, privacidade, usuario_id)
            VALUES
            ('$nome', '$descricao', '$tempo', '$dificuldade', '$caminhoImagem', '$ingredientes', '$modo_preparo', '$privacidade', '$usuario_id')";

            //executa o comando
            if(mysqli_query($conexao, $sql)){

               //deu erro volta pro index.php
               header("Location: index.php");
               exit;
            
               }else {
           
               //mostra o erro
               echo "Erro ao salvar receita: ". mysqli_error($conexao);
                }

        }else {
            //caso não salve a imagem na pasta
            echo "Erro ao enviar a imagem!";
        }
    } else {
        //caso a imagem não seja válida
        echo "Selecione uma imagem válida!";
    }
}

?>


<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Publicar Receita | Shell's Life</title>

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

    <main class="cartao">
        <h2>Publicar receita</h2>

        <!-- O atributo enctype="multipart/form-data" permite o envio de arquivos (como fotos, PDFs ou vídeos) junto com os dados de texto do formulário para o servidor -->
        <form id="formReceita" action="receita.php" method="POST" enctype="multipart/form-data">

            <p>Foto da receita</p>
            <input type="file" class="imagem" id="imagem" name="imagem" accept="image/*" required>

            <p class="dica">Escolha uma foto para mostrar sua receita.</p>

            <img id="previaImagem" alt="Prévia da foto da receita">

            <p>Nome da receita</p>
            <input type="text" class="nome" name="nome" placeholder="Ex.: Salada saudável" maxlength="100" required>

            <p>Descrição</p>
            <textarea class="desc" name="descricao" placeholder="Conte um pouco sobre essa receita..." rows="3" maxlength="500" required></textarea>

            <p>Tempo de preparo (minutos)</p>
            <input type="number" name="tempo" class="tempo" placeholder="Ex.: 20" min="1" max="1440" required>

            <p>Dificuldade</p>
            <select id="dificuldade" name="dificuldade">
                <option value="Fácil">Fácil</option>
                <option value="Média">Média</option>
                <option value="Difícil">Difícil</option>
            </select>

            <p>Privacidade da receita</p>
            <select id="privacidade" name="privacidade">
                <option value="publico">Pública</option>
                <option value="privado">Privada</option>
            </select>

            <p>Ingredientes</p>
            <textarea class="ingre" name="ingredientes" placeholder="Ex.:
            2 ovos
            1 xícara de leite
            1 colher de farinha" rows="4" required></textarea>
             <p class="dica">Aperte Enter para separar cada ingrediente</p>

            <p>Modo de preparo</p>
            <textarea class="modoP" name="modo_preparo" placeholder="Descreva o passo a passo da receita..." rows="5" required></textarea>
            <p class="dica">Aperte Enter para separar cada passo</p>

            <button type="submit">Publicar receita</button>

            <a href="index.php" class="voltar">
                Voltar para o início
            </a>

        </form>
    </main>

    <script>
        const formulario = document.querySelector('#formReceita');
        const campoImagem = document.querySelector('#imagem');
        const previaImagem = document.querySelector('#previaImagem');

        let urlPreviaAtual = null;

        // Mostra a prévia da foto selecionada
        campoImagem.addEventListener('change', function() {
            const arquivo = this.files[0];

            if (urlPreviaAtual) {
                URL.revokeObjectURL(urlPreviaAtual);
                urlPreviaAtual = null;
            }

            previaImagem.removeAttribute('src');
            previaImagem.style.display = 'none';

            if (!arquivo) {
                return;
            }

            if (!arquivo.type.startsWith('image/')) {
                alert('Selecione um arquivo de imagem válido.');
                this.value = '';
                return;
            }

            urlPreviaAtual = URL.createObjectURL(arquivo);
            previaImagem.src = urlPreviaAtual;
            previaImagem.style.display = 'block';
        });
    </script>

</body>

</html>
