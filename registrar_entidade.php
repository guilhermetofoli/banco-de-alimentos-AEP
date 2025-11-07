<?php
session_start();
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: login.php");
    exit();
}
require_once 'conexao.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tipo_entidade = mysqli_real_escape_string($conexao, $_POST['tipo_entidade']);
    $nome = mysqli_real_escape_string($conexao, $_POST['nome']);
    $documento = mysqli_real_escape_string($conexao, $_POST['documento']);
    
    $sql = "";
    $entidade = "";
    
    if ($tipo_entidade == 'doador') {
        // Usa o valor exato da sua tabela SQL: 'Pessoa Física'
        $sql = "INSERT INTO doadores (tipo_doador, nome_razao_social, documento_cpf_cnpj) 
                VALUES ('Pessoa Física', '$nome', '$documento')";
        $entidade = "Doador";
    } elseif ($tipo_entidade == 'instituicao') {
        // Insere na tabela 'instituicoes'
        $sql = "INSERT INTO instituicoes (nome_fantasia, cnpj) 
                VALUES ('$nome', '$documento')";
        $entidade = "Instituição Receptora";
    } else {
        $erro_msg = "Tipo de entidade inválido selecionado.";
        mysqli_close($conexao);
        header("Location: index.php?erro=" . urlencode($erro_msg));
        exit();
    }

    // INÍCIO DO BLOCO TRY-CATCH PARA CAPTURAR O ERRO FATAL
    try {
        $resultado_query = mysqli_query($conexao, $sql);

        if ($resultado_query) {
            // Sucesso: Redireciona para a listagem
            mysqli_close($conexao);
            header("Location: listar_entidades.php?sucesso=true&entidade=" . urlencode($entidade));
            exit();
        } else {
             // Este bloco teoricamente não é alcançado, mas é mantido como segurança
             $erro_msg = "Falha no cadastro (Erro desconhecido).";
             mysqli_close($conexao);
             header("Location: index.php?erro=" . urlencode($erro_msg));
             exit();
        }
        
    } catch (mysqli_sql_exception $e) {
        // CAPTURA A EXCEÇÃO FATAL DO MYSQL (INCLUINDO O DUPLICATE ENTRY)
        
        $erro_sql = $e->getMessage();
        
        // Formata o erro para ser mais amigável
        if (strpos($erro_sql, 'Duplicate entry') !== false) {
            $erro_msg = "Documento (CPF/CNPJ) já cadastrado. Use um documento único.";
        } else {
            // Se falhar, e não for duplicidade, mostra o erro do SQL
            $erro_msg = "Falha no cadastro: " . $erro_sql;
        }
        
        mysqli_close($conexao);
        
        // Redireciona para a index.php com a mensagem de erro (popup vermelho)
        header("Location: index.php?erro=" . urlencode($erro_msg));
        exit();
    }

} else {
    header("Location: index.php");
    exit();
}
?>