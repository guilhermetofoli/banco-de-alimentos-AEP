<?php
require_once 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Coleta e sanitização dos dados
    $doador_id = mysqli_real_escape_string($conexao, $_POST['fk_id_doador']);
    $alimento_id = mysqli_real_escape_string($conexao, $_POST['fk_id_alimento']);
    $instituicao_id = mysqli_real_escape_string($conexao, $_POST['fk_id_instituicao']);
    $quantidade = mysqli_real_escape_string($conexao, $_POST['quantidade']);
    
    // Inicialização segura da observação com valor padrão, já que o formulário não a envia
    $observacoes = "Doação via formulário.";
    
    // 2. Chamada da Stored Procedure
    $sql = "CALL sp_registrar_doacao(
        '$doador_id', 
        '$alimento_id', 
        '$instituicao_id', 
        '$quantidade', 
        '$observacoes'
    )";

    if (mysqli_query($conexao, $sql)) {
        
        // Limpa resultados pendentes da SP
        while (mysqli_more_results($conexao) && mysqli_next_result($conexao)) {;}
        
        // Sucesso: Redireciona para a lista de Doações
        mysqli_close($conexao);
        header("Location: listar_doacoes.php?sucesso=true");
        exit();
    } else {
        $erro = "Erro ao registrar a doação: " . mysqli_error($conexao);
        mysqli_close($conexao);
        die("<h2>Erro de Transação</h2><p>$erro</p><p><a href='registrar_doacao_form.php'>Voltar</a></p>");
    }
} else {
    header("Location: registrar_doacao_form.php");
    exit();
}
