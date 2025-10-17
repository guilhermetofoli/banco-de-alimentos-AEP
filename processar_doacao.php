<?php
require_once 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $doador_id = mysqli_real_escape_string($conexao, $_POST['fk_id_doador']);
    $alimento_id = mysqli_real_escape_string($conexao, $_POST['fk_id_alimento']);
    $instituicao_id = mysqli_real_escape_string($conexao, $_POST['fk_id_instituicao']);
    $quantidade = mysqli_real_escape_string($conexao, $_POST['quantidade']);
    
    // Query de INSERT na tabela DOACOES
    $sql = "INSERT INTO doacoes (fk_id_doador, fk_id_alimento, fk_id_instituicao, quantidade) 
            VALUES ('$doador_id', '$alimento_id', '$instituicao_id', '$quantidade')";

    if (mysqli_query($conexao, $sql)) {
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
?>