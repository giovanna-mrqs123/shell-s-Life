<?php

$servidor = "localhost:3306";
$usuario = "root";
$senha = "142033263#";
$nome_bd = 'shells_life';

$conexao = mysqli_connect($servidor, $usuario, $senha, $nome_bd);

if (!$conexao) {
    die("Erro na conexão: " . mysqli_connect_error());
}

echo "Conectado!";
?>
