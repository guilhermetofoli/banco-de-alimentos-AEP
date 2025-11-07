<?php
session_start();
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: login.php");
    exit();
}
require_once 'conexao.php';

$id_doacao = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id_doacao) {
    header("Location: listar_doacoes.php?erro=" . urlencode("ID de doação inválido para exclusão."));
    exit();
}

// Chamada da Stored Procedure que deleta e reverte o estoque
$sql = "CALL sp_deletar_doacao($id_doacao)";

try {
    if (mysqli_query($conexao, $sql)) {
        
        // Limpa resultados pendentes da SP
        while (mysqli_more_results($conexao) && mysqli_next_result($conexao)) {;}
        
        mysqli_close($conexao);
        header("Location: listar_doacoes.php?sucesso=true&msg=" . urlencode("Doação #$id_doacao excluída e estoque revertido com sucesso."));
        exit();
    } else {
        throw new Exception(mysqli_error($conexao));
    }
} catch (Exception $e) {
    $erro = "Falha ao excluir/reverter estoque: " . $e->getMessage();
    mysqli_close($conexao);
    header("Location: listar_doacoes.php?erro=" . urlencode($erro));
    exit();
}
?>