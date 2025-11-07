<?php
session_start();
// CÓDIGO DE PROTEÇÃO
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: login.php");
    exit();
}
require_once 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Coleta e sanitização dos dados essenciais
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $tabela = filter_input(INPUT_POST, 'tabela', FILTER_SANITIZE_STRING);
    
    // Verifica a validade mínima dos dados
    if (!$id || !in_array($tabela, ['doadores', 'instituicoes'])) {
        header("Location: listar_entidades.php?erro=" . urlencode("Dados de atualização inválidos."));
        exit();
    }

    // 2. Coleta dos campos a serem atualizados (Sanitização)
    $nome = mysqli_real_escape_string($conexao, $_POST['nome']);
    $documento = mysqli_real_escape_string($conexao, $_POST['documento']);
    
    // -----------------------------------------------------------
    // CORREÇÃO CRÍTICA: TRATAR EMAIL VAZIO COMO NULL PARA O DB
    // -----------------------------------------------------------
    $email_raw = $_POST['email'] ?? ''; 
    
    if (empty($email_raw)) {
        // Se a string estiver vazia, a variável SQL recebe 'NULL' (sem aspas)
        $email_sql = 'NULL'; 
    } else {
        // Se houver valor, sanitiza e coloca entre aspas simples
        $email_sql = "'" . mysqli_real_escape_string($conexao, $email_raw) . "'";
    }
    // -----------------------------------------------------------

    $sql_update = "";
    $pk = ($tabela == 'doadores') ? 'id_doador' : 'id_instituicao'; 
    
    // 3. Montagem da Query UPDATE condicional
    if ($tabela == 'doadores') {
        $tipo_doador = mysqli_real_escape_string($conexao, $_POST['tipo_doador']);
        
        $sql_update = "
            UPDATE doadores 
            SET 
                tipo_doador = '$tipo_doador', 
                nome_razao_social = '$nome', 
                documento_cpf_cnpj = '$documento',
                email = $email_sql  /* <-- USANDO A VARIÁVEL CORRIGIDA (pode ser NULL ou 'email@') */
            WHERE $pk = $id";
            
        $entidade = "Doador";

    } elseif ($tabela == 'instituicoes') {
        $sql_update = "
            UPDATE instituicoes 
            SET 
                nome_fantasia = '$nome', 
                cnpj = '$documento',
                email = $email_sql  /* <-- USANDO A VARIÁVEL CORRIGIDA */
            WHERE $pk = $id";
            
        $entidade = "Instituição";
    }

    // 4. Execução da Query
    if (mysqli_query($conexao, $sql_update)) {
        
        // Sucesso: Redireciona para a listagem
        mysqli_close($conexao);
        header("Location: listar_entidades.php?sucesso=true&entidade=" . urlencode("$entidade atualizada"));
        exit();
        
    } else {
        // Falha: Captura o erro do MySQL
        $erro_sql = mysqli_error($conexao);
        
        if (strpos($erro_sql, 'Duplicate entry') !== false) {
             $erro_msg = "Falha na atualização: O CPF/CNPJ informado já está sendo usado ou o e-mail é duplicado.";
        } else {
             $erro_msg = "Falha na atualização: " . $erro_sql;
        }

        mysqli_close($conexao);
        // Redireciona de volta para a tela de edição com o erro
        header("Location: editar_entidade.php?tabela=$tabela&id=$id&erro=" . urlencode($erro_msg));
        exit();
    }
    
} else {
    // Acesso direto sem POST
    header("Location: listar_entidades.php");
    exit();
}
?>