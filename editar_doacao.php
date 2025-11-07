<?php
require_once 'conexao.php';

// 1. Receber o ID da Doação a ser editada
$id_doacao = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Se o ID não for válido, redireciona de volta
if (!$id_doacao) {
    header("Location: listar_doacoes.php");
    exit();
}

// 2. Consulta PRINCIPAL: Busca os dados da doação específica
$sql_doacao = "
    SELECT 
        d.*, 
        doad.nome_razao_social AS nome_doador_atual,
        inst.nome_fantasia AS nome_instituicao_atual,
        ali.nome_alimento AS nome_alimento_atual
    FROM doacoes d
    JOIN doadores doad ON d.fk_id_doador = doad.id_doador
    JOIN instituicoes inst ON d.fk_id_instituicao = inst.id_instituicao
    JOIN alimentos ali ON d.fk_id_alimento = ali.id_alimento
    WHERE d.id_doacao = $id_doacao";

$resultado_doacao = mysqli_query($conexao, $sql_doacao);
$doacao_atual = mysqli_fetch_assoc($resultado_doacao);

// Se a doação não for encontrada, redireciona
if (!$doacao_atual) {
    header("Location: listar_doacoes.php?erro=" . urlencode("Registro de doação não encontrado."));
    exit();
}

// 3. Consultas AUXILIARES: Listas para os Dropdowns (SELECTs)
$sql_doadores = "SELECT id_doador, nome_razao_social FROM doadores ORDER BY nome_razao_social ASC";
$doadores = mysqli_query($conexao, $sql_doadores);

$sql_alimentos = "SELECT id_alimento, nome_alimento, unidade_medida FROM alimentos ORDER BY nome_alimento ASC";
$alimentos = mysqli_query($conexao, $sql_alimentos);

$sql_instituicoes = "SELECT id_instituicao, nome_fantasia FROM instituicoes ORDER BY nome_fantasia ASC";
$instituicoes = mysqli_query($conexao, $sql_instituicoes);

// Verificação de erro de consulta para depuração (opcional, mas útil)
if (!$doadores || !$alimentos || !$instituicoes) {
    die("Erro ao carregar listas de seleção: " . mysqli_error($conexao));
}

mysqli_close($conexao);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Doação #<?php echo $id_doacao; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        /* Cole o bloco <style> completo de um arquivo funcional (ex: index.html) aqui */
    </style>
</head>
<body>

<div class="container">
    <h1>Editar Doação <br> <small style="font-size: 0.6em; font-weight: 300; color: #7f8c8d;">Registro #<?php echo $id_doacao; ?></small></h1>

    <div class="nav-links">
        <a href="listar_doacoes.php">← Voltar para Consultas</a>
    </div>
    
    <div id="feedback-message"></div>

    <h2>1. Atualizar Detalhes da Doação</h2>
    
    <form action="atualizar_doacao.php" method="POST">
        
        <input type="hidden" name="id_doacao" value="<?php echo $id_doacao; ?>">

        <label for="fk_id_doador">Doador:</label>
        <select id="fk_id_doador" name="fk_id_doador" required>
            <?php while($doador = mysqli_fetch_assoc($doadores)): ?>
                <option value="<?php echo $doador['id_doador']; ?>" 
                    <?php if ($doador['id_doador'] == $doacao_atual['fk_id_doador']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($doador['nome_razao_social']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="fk_id_alimento">Alimento:</label>
        <select id="fk_id_alimento" name="fk_id_alimento" required>
            <?php while($alimento = mysqli_fetch_assoc($alimentos)): ?>
                <option value="<?php echo $alimento['id_alimento']; ?>" 
                    <?php if ($alimento['id_alimento'] == $doacao_atual['fk_id_alimento']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($alimento['nome_alimento'] . ' (' . $alimento['unidade_medida'] . ')'); ?>
                </option>
            <?php endwhile; ?>
        </select>
        
        <label for="quantidade">Quantidade:</label>
        <input type="number" id="quantidade" name="quantidade" step="0.01" min="0.01" required 
            value="<?php echo htmlspecialchars($doacao_atual['quantidade']); ?>">

        <label for="fk_id_instituicao">Instituição Receptora:</label>
        <select id="fk_id_instituicao" name="fk_id_instituicao" required>
            <?php while($instituicao = mysqli_fetch_assoc($instituicoes)): ?>
                <option value="<?php echo $instituicao['id_instituicao']; ?>" 
                    <?php if ($instituicao['id_instituicao'] == $doacao_atual['fk_id_instituicao']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($instituicao['nome_fantasia']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="observacoes">Observações:</label>
        <input type="text" id="observacoes" name="observacoes" 
            value="<?php echo htmlspecialchars($doacao_atual['observacoes']); ?>">

        <button type="submit">💾 Salvar Alterações na Doação</button>
    </form>
</div>

</body>
</html>