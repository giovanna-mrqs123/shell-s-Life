<?php
session_start();
require __DIR__ . '/conecta.php';

// só aceita envio do formulário
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.html");
    exit;
}

$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

if ($email === '' || $senha === '') {
    header("Location: login.html?erro=campos");
    exit;
}

// busca o usuário pelo e-mail (consulta preparada, protege contra SQL injection)
$stmt = mysqli_prepare($conexao, "SELECT id, nome, senha, tipo FROM usuario WHERE email = ?");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$usuario = mysqli_fetch_assoc($resultado);

// mesma mensagem para e-mail inexistente e senha errada
if (!$usuario || !password_verify($senha, $usuario['senha'])) {
    header("Location: login.html?erro=invalido");
    exit;
}

// login válido
session_regenerate_id(true);
$_SESSION['usuario_id']   = $usuario['id'];
$_SESSION['usuario_tipo'] = $usuario['tipo'];

header("Location: index.php");
exit;
?>