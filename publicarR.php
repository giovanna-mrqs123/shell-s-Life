<?php
session_start();
include 'conecta.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    die("Você precisa fazer login antes de publicar as receitas.");
}

$usuario_id = $_SESSION['usuario_id'];

$receitas = [

    [
        'nome' => 'Panqueca de Banana',
        'descricao' => 'Panquecas macias de banana e aveia, perfeitas para o café da manhã.',
        'ingredientes' => "1 banana madura\n1 ovo\n3 colheres de aveia\n1 colher de chá de canela\n1 colher de chá de fermento",
        'modo_preparo' => " Amasse a banana.\n Acrescente o ovo, a aveia e a canela.\n Misture até formar uma massa homogênea.\n Adicione o fermento.\n Coloque pequenas porções em uma frigideira untada.\n Doure dos dois lados.",
        'tempo' => 15,
        'dificuldade' => 'Fácil',
        'imagem' => 'uploads/panqueca banana.png',
        'privacidade' => 'publico'
    ],

    [
        'nome' => 'Sanduíche Natural de Frango',
        'descricao' => 'Sanduíche prático recheado com frango desfiado e vegetais.',
        'ingredientes' => "2 fatias de pão integral\n100 g de frango cozido e desfiado\n1 colher de requeijão\nAlface\n2 rodelas de tomate\nSal a gosto",
        'modo_preparo' => " Misture o frango com o requeijão.\n Tempere com sal.\n Coloque o recheio sobre uma fatia de pão.\n Acrescente o tomate e a alface.\n Feche com a outra fatia.",
        'tempo' => 20,
        'dificuldade' => 'Fácil',
        'imagem' => 'uploads/sanduiche frango.jpeg',
        'privacidade' => 'publico'
    ],

    [
        'nome' => 'Muffin de Aveia e Maçã',
        'descricao' => 'Bolinhos fofinhos de maçã com aveia e um toque de canela.',
        'ingredientes' => "1 maçã picada\n1 ovo\n1/2 xícara de aveia\n1/4 de xícara de leite\n2 colheres de açúcar\n1 colher de chá de fermento\nCanela a gosto",
        'modo_preparo' => " Misture o ovo, o leite e o açúcar.\n Acrescente a aveia e a maçã.\n Adicione a canela e o fermento.\n Distribua em forminhas.\n Asse a 180 °C por aproximadamente 20 minutos.",
        'tempo' => 30,
        'dificuldade' => 'Fácil',
        'imagem' => 'uploads/Muffin aveia.jpeg',
        'privacidade' => 'publico'
    ],

    [
        'nome' => 'Crepioca de Queijo',
        'descricao' => 'Uma opção rápida de crepioca com recheio de queijo derretido.',
        'ingredientes' => "1 ovo\n2 colheres de goma de tapioca\n2 fatias de queijo\nSal a gosto\nOrégano a gosto",
        'modo_preparo' => " Bata o ovo com a goma de tapioca.\n Tempere com sal.\n Despeje em uma frigideira aquecida.\n Espere firmar e vire.\n Acrescente o queijo e o orégano.\n Dobre ao meio.",
        'tempo' => 10,
        'dificuldade' => 'Fácil',
        'imagem' => 'uploads/crepioca queijo.jpeg',
        'privacidade' => 'publico'
    ],

    [
        'nome' => 'Smoothie de Morango',
        'descricao' => 'Bebida cremosa e refrescante feita com morangos e banana.',
        'ingredientes' => "1 xícara de morangos\n1 banana\n200 ml de leite\n2 colheres de iogurte natural\nGelo a gosto",
        'modo_preparo' => " Lave e corte os morangos.\n Descasque e corte a banana.\n Coloque todos os ingredientes no liquidificador.\n Bata até ficar cremoso.\n Sirva em seguida.",
        'tempo' => 5,
        'dificuldade' => 'Fácil',
        'imagem' => 'uploads/smoothie morango.jpeg',
        'privacidade' => 'publico'
    ],

    [
        'nome' => 'Bolinho de Batata-doce',
        'descricao' => 'Bolinhos assados de batata-doce com textura macia e tempero caseiro.',
        'ingredientes' => "2 batatas-doces médias\n1/2 xícara de farinha de aveia\n1 colher de azeite\nSal a gosto\nOrégano a gosto\nQueijo em cubinhos",
        'modo_preparo' => " Cozinhe as batatas-doces até ficarem macias.\n Amasse até formar um purê.\n Acrescente a farinha, o azeite e os temperos.\n Misture até conseguir modelar.\n Faça bolinhas e coloque queijo no centro.\n Asse a 180 °C por aproximadamente 20 minutos.",
        'tempo' => 35,
        'dificuldade' => 'Média',
        'imagem' => 'uploads/bolinho batata.jpeg',
        'privacidade' => 'publico'
    ],

    [
        'nome' => 'Salada de Macarrão com Legumes',
        'descricao' => 'Salada colorida de macarrão com legumes e molho cremoso.',
        'ingredientes' => "2 xícaras de macarrão cozido\n1/2 cenoura ralada\n1/2 xícara de milho\n1/2 xícara de ervilha\n3 colheres de iogurte natural\nSal e cheiro-verde a gosto",
        'modo_preparo' => " Cozinhe o macarrão e espere esfriar.\n Acrescente a cenoura, o milho e a ervilha.\n Misture o iogurte natural.\n Tempere com sal e cheiro-verde.\n Leve à geladeira antes de servir.",
        'tempo' => 25,
        'dificuldade' => 'Fácil',
        'imagem' => 'uploads/macarrao.jpeg',
        'privacidade' => 'publico'
    ],

    [
        'nome' => 'Overnight Oats de Chocolate',
        'descricao' => 'Preparação cremosa de aveia com chocolate, feita para deixar na geladeira durante a noite.',
        'ingredientes' => "4 colheres de aveia\n150 ml de leite\n1 colher de cacau em pó\n1 colher de mel\n1/2 banana picada",
        'modo_preparo' => " Coloque a aveia em um pote.\n Acrescente o leite e o cacau.\n Misture o mel.\n Adicione a banana picada.\n Tampe e deixe na geladeira por pelo menos 4 horas.\n Sirva gelado.",
        'tempo' => 5,
        'dificuldade' => 'Fácil',
        'imagem' => 'uploads/overnight chocolate.jpeg',
        'privacidade' => 'publico'
    ],

    [
        'nome' => 'Pizza de Frigideira',
        'descricao' => 'Pizza individual feita na frigideira, com massa simples e cobertura de queijo e tomate.',
        'ingredientes' => "1 ovo\n4 colheres de farinha de trigo\n3 colheres de leite\n1 colher de chá de fermento\n2 colheres de molho de tomate\nQueijo mussarela\nTomate e orégano",
        'modo_preparo' => " Misture o ovo, a farinha e o leite.\n Acrescente o fermento.\n Despeje a massa em uma frigideira untada.\n Cozinhe em fogo baixo até firmar.\n Vire a massa e espalhe o molho.\n Adicione o queijo, o tomate e o orégano.\n Tampe até o queijo derreter.",
        'tempo' => 15,
        'dificuldade' => 'Fácil',
        'imagem' => 'uploads/pizza.jpeg',
        'privacidade' => 'publico'
    ],

    [
        'nome' => 'Bolo de Caneca de Banana e Cacau',
        'descricao' => 'Bolo individual de banana com cacau, preparado rapidamente no micro-ondas.',
        'ingredientes' => "1 banana madura\n1 ovo\n2 colheres de aveia\n1 colher de cacau em pó\n1/2 colher de chá de fermento",
        'modo_preparo' => " Amasse a banana em uma caneca grande.\n Acrescente o ovo e misture.\n Adicione a aveia e o cacau.\n Coloque o fermento e misture delicadamente.\n Leve ao micro-ondas por aproximadamente 2 a 3 minutos.\n Espere amornar.",
        'tempo' => 7,
        'dificuldade' => 'Fácil',
        'imagem' => 'uploads/bolo de caneca.webp',
        'privacidade' => 'publico'
    ]

];

// Prepara a consulta para verificar se a receita já existe
$verificar = mysqli_prepare($conexao,
    "SELECT id FROM receita WHERE nome = ? AND usuario_id = ?"
);

// Prepara a consulta para cadastrar a receita
$inserir = mysqli_prepare($conexao,
    "INSERT INTO receita
    (nome, descricao, tempo, dificuldade, imagem, ingredientes, modo_preparo, usuario_id, privacidade)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
);

// Conta quantas receitas foram cadastradas
$cadastradas = 0;

foreach ($receitas as $receita) {

    // Verifica se essa receita já foi cadastrada pelo usuário
    mysqli_stmt_bind_param($verificar, "si",
        $receita['nome'], $usuario_id
    );

    mysqli_stmt_execute($verificar);

    $resultado = mysqli_stmt_get_result($verificar);

    // Só cadastra se ainda não existir
    if (mysqli_num_rows($resultado) == 0) {

        mysqli_stmt_bind_param($inserir, "ssissssis",
            $receita['nome'],
            $receita['descricao'],
            $receita['tempo'],
            $receita['dificuldade'],
            $receita['imagem'],
            $receita['ingredientes'],
            $receita['modo_preparo'],
            $usuario_id,
            $receita['privacidade']
        );

        if (mysqli_stmt_execute($inserir)) {
            $cadastradas++;
        } else {
            die("Erro ao cadastrar receita: " . mysqli_stmt_error($inserir));
        }
    }
}

echo "$cadastradas receitas cadastradas com sucesso!";

// Fecha as consultas
mysqli_stmt_close($verificar);
mysqli_stmt_close($inserir);

?>