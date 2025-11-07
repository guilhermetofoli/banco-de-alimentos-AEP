<?php
session_start();
// CÓDIGO DE PROTEÇÃO
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: login.php");
    exit();
}
require_once 'conexao.php';

// 1. Verifica se os dados do formulário foram enviados via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 2. Coleta e sanitiza os dados do formulário
    // Usa a função mysqli_real_escape_string para evitar ataques de SQL Injection
    $nome = mysqli_real_escape_string($conexao, $_POST['doador_nome']);
    $tipo = mysqli_real_escape_string($conexao, $_POST['tipo_doador']);
    $documento = mysqli_real_escape_string($conexao, $_POST['documento']);
    
    // Simplificando: Assumimos que no MVP inicial, o Doador é o foco. 
    // Outros campos (telefone, email) seriam adicionados aqui se o formulário fosse completo.

    // 3. Monta a query SQL de Inserção (INSERT)
    // Insere na tabela 'doadores' criada no script SQL anterior
    $sql = "INSERT INTO doadores (tipo_doador, nome_razao_social, documento_cpf_cnpj) 
            VALUES ('$tipo', '$nome', '$documento')";

    // 4. Executa a query no banco de dados
    if (mysqli_query($conexao, $sql)) {
        $mensagem = "Doador '$nome' registrado com sucesso no banco de dados!";
        $sucesso = true;
    } else {
        $mensagem = "Erro ao registrar doador: " . mysqli_error($conexao);
        $sucesso = false;
    }

    // 5. Fecha a conexão com o banco de dados
    mysqli_close($conexao);

} else {
    // Caso a página seja acessada diretamente sem o formulário
    $mensagem = "Acesso inválido. Utilize o formulário de cadastro.";
    $sucesso = false;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado do Registro</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        /* [CÓDIGO CSS BÁSICO PARA EXIBIR A MENSAGEM] */
        body { font-family: 'Roboto', sans-serif; background-color: #ecf0f1; text-align: center; padding: 50px; }
        .container { max-width: 600px; margin: auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        a { color: #27ae60; text-decoration: none; font-weight: bold; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Resultado da Operação</h2>
        
        <?php if ($sucesso): ?>
            <div class="alert-success">
                <h3>Sucesso!</h3>
                <p><?php echo $mensagem; ?></p>
            </div>
        <?php else: ?>
            <div class="alert-danger">
                <h3>Erro!</h3>
                <p><?php echo $mensagem; ?></p>
            </div>
        <?php endif; ?>

        <p><a href="index.php">Voltar para o Formulário de Cadastro</a></p>
    </div>
</body>
</html>