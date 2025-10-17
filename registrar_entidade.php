<?php
require_once 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tipo_entidade = mysqli_real_escape_string($conexao, $_POST['tipo_entidade']);
    $nome = mysqli_real_escape_string($conexao, $_POST['nome']);
    $documento = mysqli_real_escape_string($conexao, $_POST['documento']);
    
    $sql = "";
    
    if ($tipo_entidade == 'doador') {
        // Insere na tabela 'doadores'
        $sql = "INSERT INTO doadores (tipo_doador, nome_razao_social, documento_cpf_cnpj) 
                VALUES ('Pessoa Física', '$nome', '$documento')";
        $entidade = "Doador (Pessoa Física)";
    } elseif ($tipo_entidade == 'instituicao') {
        // Insere na tabela 'instituicoes'
        $sql = "INSERT INTO instituicoes (nome_fantasia, cnpj) 
                VALUES ('$nome', '$documento')";
        $entidade = "Instituição Receptora";
    } else {
        die("Tipo de entidade inválido.");
    }

    if (mysqli_query($conexao, $sql)) {
        // Sucesso: Redireciona para a listagem
        mysqli_close($conexao);
        header("Location: listar_entidades.php?sucesso=true&entidade=" . urlencode($entidade));
        exit();
    } else {
        // Erro
        $erro = "Erro ao registrar $entidade: " . mysqli_error($conexao);
        mysqli_close($conexao);
        die("<h2>Erro de Cadastro</h2><p>$erro</p><p><a href='index.html'>Voltar</a></p>");
    }

} else {
    header("Location: index.html");
    exit();
}
?>