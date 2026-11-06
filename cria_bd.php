<?php
$servidor = "localhost:3306";
$usuario = "root";
$senha = "142033263#";

$conexao = mysqli_connect($servidor, $usuario, $senha);

if (!$conexao) {
    die("Erro na conexão: " . mysqli_connect_error());
}

$sql = "CREATE DATABASE shells_life";

if (mysqli_query($conexao, $sql)) {
    echo "Banco criado com sucesso!";
} else {
    echo "Erro ao criar BD: " . mysqli_error($conexao);
}

mysqli_close($conexao);
?>
