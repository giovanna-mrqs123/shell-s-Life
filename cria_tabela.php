<?php
include 'conecta.php';

echo "<br>";

//tabela usuário
$sql = 'CREATE TABLE IF NOT EXISTS usuario (
            id INT NOT NULL AUTO_INCREMENT,
            nome VARCHAR(100) NOT NULL,
            email VARCHAR(100) NULL,
            PRIMARY KEY (id))
            ENGINE = InnoDB';

if (mysqli_query($conexao, $sql) === TRUE) {
    echo "Tabela usuario criada com sucesso!";
} else {
    echo "Erro ao criar a tabela: " . $conexao->error;
}

echo "<br>";
//tabela receita


$sql = 'CREATE TABLE IF NOT EXISTS receita (
            id INT NOT NULL AUTO_INCREMENT,
            nome VARCHAR(100) NOT NULL,
            modo_preparo TEXT NOT NULL,
            ingredientes TEXT NOT NULL,
            privacidade VARCHAR(20),
            descricao TEXT,
            usuario_id INT NOT NULL,
            PRIMARY KEY (id),
            FOREIGN KEY (usuario_id) REFERENCES usuario(id))
            ENGINE = InnoDB';

if (mysqli_query($conexao, $sql) === TRUE) {
    echo "Tabela receita criada com sucesso!";
} else {
    echo "Erro ao criar a tabela: " . $conexao->error;
}

echo "<br>";


$sql = 'CREATE TABLE IF NOT EXISTS interacao (
            id INT NOT NULL AUTO_INCREMENT,
            tipo VARCHAR(20) NOT NULL,
            nota INT,
            data_interacao DATETIME NOT NULL,
            usuario_id INT NOT NULL,
            receita_id INT NOT NULL,
            PRIMARY KEY (id),
            FOREIGN KEY (usuario_id) REFERENCES usuario(id),
            FOREIGN KEY (receita_id) REFERENCES receita(id))
            ENGINE = InnoDB';

if (mysqli_query($conexao, $sql) === TRUE) {
    echo "Tabela interacao criada com sucesso!";
} else {
    echo "Erro ao criar a tabela: " . $conexao->error;
}

echo "<br>";

$sql = 'CREATE TABLE IF NOT EXISTS livro_receitas (
            id INT NOT NULL AUTO_INCREMENT,
            nome VARCHAR(100) NOT NULL,
            descricao TEXT,
            usuario_id INT NOT NULL,
            PRIMARY KEY (id),
            FOREIGN KEY (usuario_id) REFERENCES usuario(id))
            ENGINE = InnoDB';

if (mysqli_query($conexao, $sql) === TRUE) {
    echo "Tabela livro_receitas criada com sucesso!";
} else {
    echo "Erro ao criar a tabela: " . $conexao->error;
}

echo "<br>";

$sql = 'CREATE TABLE IF NOT EXISTS livro_receita (
            livro_receitas_id INT NOT NULL,
            receita_id INT NOT NULL,
            PRIMARY KEY (livro_receitas_id, receita_id),
            FOREIGN KEY (livro_receitas_id) REFERENCES livro_receitas(id),
            FOREIGN KEY (receita_id) REFERENCES receita(id))
            ENGINE = InnoDB';

if (mysqli_query($conexao, $sql) === TRUE) {
    echo "Tabela livro_receita criada com sucesso!";
} else {
    echo "Erro ao criar a tabela: " . $conexao->error;
}

echo "<br>";

$sql = 'CREATE TABLE IF NOT EXISTS produto (
            id INT NOT NULL AUTO_INCREMENT,
            nome VARCHAR(100) NOT NULL,
            descricao TEXT,
            tabela_nutricional TEXT NOT NULL,
            PRIMARY KEY (id))
            ENGINE = InnoDB';

if (mysqli_query($conexao, $sql) === TRUE) {
    echo "Tabela produto criada com sucesso!";
} else {
    echo "Erro ao criar a tabela: " . $conexao->error;
}

echo "<br>";

mysqli_close($conexao);



?>
