<?php
/*
 * RODE UMA VEZ e depois APAGUE este arquivo do servidor.
 * Ele:
 *  1. garante que a tabela usuario tem as colunas "senha" e "tipo"
 *  2. garante que a tabela produto tem a coluna "imagem"
 *  3. cria o usuário admin (ou promove o e-mail a admin, se já existir)
 */
require __DIR__ . '/conecta.php';

// ====== EDITE AQUI ======
$adminNome  = "Administrador";
$adminEmail = "admin@gmail.com";
$adminSenha = "admin123";   // use uma senha sua, com 6+ caracteres
// ========================

function colunaExiste($conexao, $tabela, $coluna)
{
    $resultado = mysqli_query($conexao, "SHOW COLUMNS FROM `$tabela` LIKE '$coluna'");
    return $resultado && mysqli_num_rows($resultado) > 0;
}

function rodar($conexao, $sql, $mensagem)
{
    if (mysqli_query($conexao, $sql)) {
        echo "OK: $mensagem<br>";
    } else {
        die("Erro em '$mensagem': " . mysqli_error($conexao));
    }
}

if (strlen($adminSenha) < 6 || $adminSenha === "troque-esta-senha") {
    die("Defina uma senha sua na variável \$adminSenha antes de rodar.");
}

// 1. colunas da tabela usuario
if (!colunaExiste($conexao, 'usuario', 'senha')) {
    rodar($conexao, "ALTER TABLE usuario ADD COLUMN senha VARCHAR(255) NOT NULL DEFAULT ''", "coluna senha criada");
} else {
    echo "Coluna senha já existe.<br>";
}

if (!colunaExiste($conexao, 'usuario', 'tipo')) {
    rodar($conexao, "ALTER TABLE usuario ADD COLUMN tipo VARCHAR(20) NOT NULL DEFAULT 'usuario'", "coluna tipo criada");
} else {
    echo "Coluna tipo já existe.<br>";
}

// 2. coluna de imagem em produto
if (!colunaExiste($conexao, 'produto', 'imagem')) {
    rodar($conexao, "ALTER TABLE produto ADD COLUMN imagem VARCHAR(255) NULL", "coluna imagem criada em produto");
} else {
    echo "Coluna imagem de produto já existe.<br>";
}

// 3. usuário admin
$senhaProtegida = password_hash($adminSenha, PASSWORD_DEFAULT);

$stmt = mysqli_prepare($conexao, "SELECT id FROM usuario WHERE email = ?");
mysqli_stmt_bind_param($stmt, "s", $adminEmail);
mysqli_stmt_execute($stmt);
$existente = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if ($existente) {
    $stmt = mysqli_prepare($conexao, "UPDATE usuario SET nome = ?, senha = ?, tipo = 'admin' WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "ssi", $adminNome, $senhaProtegida, $existente['id']);
    mysqli_stmt_execute($stmt);
    echo "OK: usuário $adminEmail já existia e agora é admin (senha atualizada).<br>";
} else {
    $stmt = mysqli_prepare($conexao, "INSERT INTO usuario (nome, email, senha, tipo) VALUES (?, ?, ?, 'admin')");
    mysqli_stmt_bind_param($stmt, "sss", $adminNome, $adminEmail, $senhaProtegida);
    if (!mysqli_stmt_execute($stmt)) {
        die("Erro ao criar admin: " . mysqli_error($conexao));
    }
    echo "OK: admin $adminEmail criado.<br>";
}

echo "<br><strong>Pronto! Agora APAGUE o arquivo criar_admin.php.</strong>";
?>