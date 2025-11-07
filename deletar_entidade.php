<?php
session_start();
// CÓDIGO DE PROTEÇÃO
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: login.php");
    exit();
}
require_once 'conexao.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$tabela = filter_input(INPUT_GET, 'tabela', FILTER_SANITIZE_STRING);

if (!$id || !in_array($tabela, ['doadores', 'instituicoes'])) {
    header("Location: listar_entidades.php?erro=" . urlencode("Parâmetros de exclusão inválidos."));
    exit();
}

if ($tabela == 'doadores') {
    $conexao->query("DELETE FROM doacoes WHERE fk_id_doador = $id");
    $sql_delete = "DELETE FROM doadores WHERE id_doador = $id";
    $entidade = "Doador";
} else {
    $conexao->query("DELETE FROM retiradas WHERE fk_id_instituicao = $id");
    $sql_delete = "DELETE FROM instituicoes WHERE id_instituicao = $id";
    $entidade = "Instituição";
}

if (mysqli_query($conexao, $sql_delete)) {
    mysqli_close($conexao);
    header("Location: listar_entidades.php?sucesso=true&entidade=" . urlencode("$entid1ade excluído com sucesso!"));
    exit();
} else {
    $erro = "Erro ao excluir $entidade: " . mysqli_error($conexao);
    mysqli_close($conexao);
    header("Location: listar_entidades.php?erro=" . urlencode($erro));
    exit();
}
?>