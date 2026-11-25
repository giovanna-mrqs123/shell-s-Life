
<?php
include 'conecta.php';

if (isset($_POST['redefinir'])) {
    $email = mysqli_real_escape_string($conexao, $_POST['email']);
    $senha = $_POST['senha'];

    if (strlen($senha) < 6) {
        die("A senha precisa ter pelo menos 6 caracteres!");
    }

    $senhaProtegida = password_hash($senha, PASSWORD_DEFAULT);

    $sql = "UPDATE usuario SET senha = '$senhaProtegida'
            WHERE email = '$email'";

    $resultado = mysqli_query($conexao, $sql);

    if ($resultado && mysqli_affected_rows($conexao) > 0) {
        echo "Senha alterada com sucesso!";
    } else {
        echo "Não foi possível alterar a senha. Confira o email.";
    }
}
?>

<form method="POST">
    <p>Email da conta:</p>
    <input type="email" name="email" required>

    <p>Nova senha:</p>
    <input type="password" name="senha" required>

    <button type="submit" name="redefinir">Redefinir senha</button>
</form>
