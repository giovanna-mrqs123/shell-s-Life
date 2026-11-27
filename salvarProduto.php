<?php
session_start();
require __DIR__ . '/conecta.php';

if (($_SESSION['usuario_tipo'] ?? '') !== 'admin') {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: produto.php");
    exit;
}

$nome      = trim($_POST['nome'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');
$tabela    = $_POST['tabela_nutricional'] ?? '';

if ($nome === '' || $descricao === '' || $tabela === '') {
    die("Preencha todos os campos.");
}

if (!isset($_FILES['imagem']) || $_FILES['imagem']['error'] !== UPLOAD_ERR_OK) {
    die("Erro ao enviar a imagem.");
}

$extensoes = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
$ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));

if (!in_array($ext, $extensoes) || getimagesize($_FILES['imagem']['tmp_name']) === false) {
    die("Envie um arquivo de imagem válido.");
}

$pasta = __DIR__ . '/uploads/';
if (!is_dir($pasta)) {
    mkdir($pasta, 0755, true);
}

$nomeArquivo = bin2hex(random_bytes(8)) . '.' . $ext;

if (!move_uploaded_file($_FILES['imagem']['tmp_name'], $pasta . $nomeArquivo)) {
    die("Não foi possível salvar a imagem.");
}

$caminho = 'uploads/' . $nomeArquivo;

$stmt = mysqli_prepare($conexao, "INSERT INTO produto (nome, descricao, tabela_nutricional, imagem) VALUES (?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "ssss", $nome, $descricao, $tabela, $caminho);

if (!mysqli_stmt_execute($stmt)) {
    die("Erro ao salvar o produto: " . mysqli_error($conexao));
}

header("Location: index.php#produtos");
exit;
?>