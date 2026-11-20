<?php
session_start();
include 'conecta.php';

$nome = $_POST['nome'];
$nome = mysqli_real_escape_string($conexao, $nome);
$email = $_POST['email'];
$email = mysqli_real_escape_string($conexao, $email);
$senha = $_POST['senha'];

//verifica email
$sqlVerifica = "SELECT * FROM usuario WHERE email = '$email'";
$resultadoVerifica = mysqli_query($conexao, $sqlVerifica);

if (mysqli_num_rows($resultadoVerifica) > 0) {

    $usuario = mysqli_fetch_assoc($resultadoVerifica);

    if (password_verify($senha, $usuario['senha'])) {

        $_SESSION['usuario_id'] = $usuario['id'];

        header("Location: index.php");
        exit;

    } else {
        die("Senha incorreta!");
    }
}

// Protege a senha antes de salvar
$senhaProtegida = password_hash($senha, PASSWORD_DEFAULT);


// Insere o usuário no banco de dados
$sqlCadastrar = "INSERT INTO usuario (nome, email, senha)
                 VALUES ('$nome', '$email', '$senhaProtegida')";

$resultadoCadastrar = mysqli_query($conexao, $sqlCadastrar);
if (!$resultadoCadastrar) {
    die("Erro ao realizar cadastro: " . mysqli_error($conexao));
}
if ($resultadoCadastrar) {
    $_SESSION['usuario_id'] = mysqli_insert_id($conexao);

    header("Location: index.php");
    exit;
}

if($nome == "" || $email == "" || $senha == ""){
    die("Por favor, preencha todos os campos.");
}
//senha maior que 6 caracteres
if (strlen($senha) < 6) {
    die("A senha precisa ter pelo menos 6 caracteres!");
}

?>
