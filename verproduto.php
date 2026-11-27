<?php
session_start();
require __DIR__ . '/conecta.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = mysqli_prepare($conexao, "SELECT * FROM produto WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$produto = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

$t = $produto ? json_decode($produto['tabela_nutricional'], true) : null;

$linhas = [
    'energia'             => ['Valor energético (kcal)', ''],
    'carboidratos'        => ['Carboidratos (g)', ''],
    'acucaresTotais'      => ['Açúcares totais (g)', 'indentado'],
    'acucaresAdicionados' => ['Açúcares adicionados (g)', 'indentado2'],
    'proteinas'           => ['Proteínas (g)', ''],
    'gordurasTotais'      => ['Gorduras totais (g)', ''],
    'gordurasSaturadas'   => ['Gorduras saturadas (g)', 'indentado'],
    'gordurasTrans'       => ['Gorduras trans (g)', 'indentado'],
    'fibra'               => ['Fibra alimentar (g)', ''],
    'sodio'               => ['Sódio (mg)', ''],
];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver produto | Shell's Life</title>

    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: rgb(247, 246, 228);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            margin: 0;
            padding: 20px 0;
        }

        .cartao {
            width: 350px;
            padding: 15px;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10%;
            display: flex;
            flex-direction: column;
            justify-content: start;
            align-items: center;
        }

        .cartao img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            border-radius: 12px;
        }

        h2 {
            text-align: center;
            color: #2e2e2a;
            margin: 16px 0 4px;
        }

        p {
            margin: 8px 0;
            font-size: 16px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            width: 100%;
        }

        .voltar {
            align-self: flex-start;
            text-decoration: none;
            color: #4a7c3a;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .voltar:hover {
            color: #3f6931;
        }

        .badge {
            padding: 5px 10px;
            border-radius: 8px;
            color: white;
            font-size: 14px;
            background-color: #b8975a;
        }

        .titulo-secao {
            font-weight: 700;
            margin-top: 14px;
            margin-bottom: 2px;
        }

        .tabela {
            white-space: pre-line;
        }

        button {
            margin-top: 20px;
            background-color: #4a7c3a;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 10px;
            width: 100%;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
        }

        button:hover {
            background-color: #3f6931;
        }

        .tabelaNutri {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #000;
            background: #fff;
            color: #000;
            font-size: 13px;
            margin-top: 6px;
        }

        .tabelaNutri th {
            text-align: left;
            font-size: 15px;
            padding: 6px;
            border-bottom: 1px solid #000;
        }

        .tabelaNutri td {
            padding: 4px 6px;
            border-bottom: 1px solid #000;
            text-align: right;
        }

        .tabelaNutri td:first-child {
            text-align: left;
        }

        .tabelaNutri .colunas td {
            font-weight: 700;
            text-align: right;
        }

        .tabelaNutri td.indentado, .tabelaNutri td.indentado2 {
            padding-left: 6px;
        }

        .tabelaNutri tfoot td {
            font-size: 11px;
            text-align: left;
            border-bottom: none;
        }
    </style>
</head>

<body>
    <div class="cartao">
        <?php if (!$produto) { ?>
        <p>Produto não encontrado.</p>

        <button onclick="window.location.href='index.php#produtos'">Voltar</button>

        <?php } else { ?>
        <a href="index.php#produtos" class="voltar">Voltar</a>
        <img src="<?php echo htmlspecialchars($produto['imagem']); ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>">
        <h2><?php echo htmlspecialchars($produto['nome']); ?></h2>
        <p><?php echo htmlspecialchars($produto['descricao']); ?></p>

        <p class="titulo-secao">Tabela nutricional</p>

        <?php if (is_array($t)) { ?>
        <table class="tabelaNutri">
            <thead>
                <tr><th colspan="4">INFORMAÇÃO NUTRICIONAL</th></tr>
                <tr><td colspan="4">Porções por embalagem: <?php echo htmlspecialchars($t['porcoesEmbalagem']); ?></td></tr>
                <tr><td colspan="4">Porção: <?php echo htmlspecialchars($t['porcao']); ?></td></tr>
                <tr class="colunas"><td></td><td>100 g</td><td>Porção</td><td>%VD*</td></tr>
            </thead>
            <tbody>
                <?php foreach ($linhas as $chave => [$rotulo, $classe]) {
                    $d = $t['valores'][$chave] ?? []; ?>
                    <tr>
                        <td class="<?php echo $classe; ?>"><?php echo $rotulo; ?></td>
                        <td><?php echo htmlspecialchars($d['por100'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($d['porPorcao'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($d['vd'] ?? ''); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr><td colspan="4">*Percentual de valores diários fornecidos pela porção.</td></tr>
            </tfoot>
        </table>
        <?php } ?>
        <?php } ?>
    </div>
</body>

    <script src="sistema.js"></script>
    <script>
        function excluirProduto(indice) {
            if (!ehSistema()) {
                return;
            }

            if (!confirm('Tem certeza que deseja excluir este produto?')) {
                return;
            }

            const produtos = JSON.parse(localStorage.getItem('produtos')) || [];
            produtos.splice(indice, 1);
            localStorage.setItem('produtos', JSON.stringify(produtos));

            window.location.href = 'index.html#produtos';
        }

        function editarProduto(indice) {
            if (!ehSistema()) {
                return;
            }

            const produtos = JSON.parse(localStorage.getItem('produtos')) || [];
            const produto = produtos[indice];

            const novoNome = prompt('Digite o novo nome do produto:', produto.nome);

            if (novoNome === null || novoNome.trim() === '') {
                return;
            }

            produto.nome = novoNome.trim();
            localStorage.setItem('produtos', JSON.stringify(produtos));

            location.reload();
        }

        function verProduto() {
            const params = new URLSearchParams(window.location.search);
            const id = params.get('id');

            const produtos = JSON.parse(localStorage.getItem('produtos')) || [];
            const produto = produtos[id];
            const cartao = document.getElementById('cartProduto');

            if (!produto) {
                cartao.innerHTML = `
                    <p>Produto não encontrado.</p>
                    <button onclick="window.location.href='index.html#produtos'">Voltar</button>
                `;
                return;
            }

            // Editar e Excluir só aparecem para o Sistema
            const botoesSistema = ehSistema() ?
                `<button onclick="editarProduto(${id})">Editar</button>
                 <button onclick="excluirProduto(${id})">Excluir</button>` :
                '';

            cartao.innerHTML = `
                <a href="index.html#produtos" class="voltar">Voltar</a>
                <img src="${produto.imagem}" alt="${produto.nome}">
                <h2>${produto.nome}</h2>
                <p>${produto.descricao}</p>

                <p class="titulo-secao">Tabela nutricional</p>
                <div id="areaTabela"></div>

                ${botoesSistema}
            `;

            // Preenche a tabela nutricional
            const t = produto.tabelaNutricional;
            const areaTabela = document.getElementById('areaTabela');

            if (typeof t === 'object') {
                const tabela = document.getElementById('modeloTabela').content.cloneNode(true);

                tabela.querySelector('.porcoes').textContent = t.porcoesEmbalagem;
                tabela.querySelector('.porcao').textContent = t.porcao;

                tabela.querySelectorAll('tbody tr').forEach(linha => {
                    const d = t.valores[linha.dataset.id] || {};
                    linha.querySelector('.v100').textContent = d.por100 ?? '';
                    linha.querySelector('.vporcao').textContent = d.porPorcao ?? '';
                    linha.querySelector('.vvd').textContent = d.vd ?? '';
                });

                areaTabela.appendChild(tabela);
            } else {
                // produtos antigos, salvos com a tabela em texto
                areaTabela.innerHTML = `<p class="tabela">${t}</p>`;
            }
        }

        verProduto();
    </script>

</body>
</html>
