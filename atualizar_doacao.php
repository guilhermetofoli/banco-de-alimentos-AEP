<?php
require_once 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Coleta e sanitização dos dados, incluindo o ID da doação
    $id_doacao = filter_input(INPUT_POST, 'id_doacao', FILTER_VALIDATE_INT);
    
    // Se o ID da doação não vier, para o script
    if (!$id_doacao) {
        header("Location: listar_doacoes.php?erro=" . urlencode("ID da doação para atualização é inválido."));
        exit();
    }
    
    // Coleta dos outros campos (que precisam ser tratados para atualização)
    $doador_id = mysqli_real_escape_string($conexao, $_POST['fk_id_doador']);
    $alimento_id = mysqli_real_escape_string($conexao, $_POST['fk_id_alimento']);
    $instituicao_id = mysqli_real_escape_string($conexao, $_POST['fk_id_instituicao']);
    $quantidade = mysqli_real_escape_string($conexao, $_POST['quantidade']);
    $observacoes = mysqli_real_escape_string($conexao, $_POST['observacoes']);

    
    // 2. Query de UPDATE no MySQL
    // NOTA: Esta query só atualiza a TABELA DOACOES.
    // Em um sistema completo, qualquer mudança na QUANTIDADE precisaria reverter o estoque antigo 
    // e aplicar a nova (o que exigiria uma Stored Procedure complexa).
    // Para o MVP, focamos na atualização direta da linha.
    
    $sql_update = "
        UPDATE doacoes 
        SET 
            fk_id_doador = '$doador_id', 
            fk_id_alimento = '$alimento_id', 
            fk_id_instituicao = '$instituicao_id', 
            quantidade = '$quantidade', 
            observacoes = '$observacoes'
        WHERE id_doacao = $id_doacao";

    // 3. Execução da Query
    if (mysqli_query($conexao, $sql_update)) {
        
        // Redireciona para a listagem com mensagem de sucesso
        mysqli_close($conexao);
        header("Location: listar_doacoes.php?sucesso=true&msg=" . urlencode("Doação #$id_doacao atualizada com sucesso!"));
        exit();
        
    } else {
        // Redireciona de volta para a edição com mensagem de erro
        $erro = "Falha na atualização do banco: " . mysqli_error($conexao);
        mysqli_close($conexao);
        header("Location: editar_doacao.php?id=$id_doacao&erro=" . urlencode($erro));
        exit();
    }
    
} else {
    // Acesso direto sem POST
    header("Location: listar_doacoes.php");
    exit();
}
?>