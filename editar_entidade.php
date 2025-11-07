<?php
session_start();
// CÓDIGO DE PROTEÇÃO
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: login.php");
    exit();
}
require_once 'conexao.php';

// 1. Receber e validar os parâmetros da URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$tabela = filter_input(INPUT_GET, 'tabela', FILTER_SANITIZE_STRING);

// Verifica se os parâmetros são válidos e se a tabela é reconhecida
if (!$id || !in_array($tabela, ['doadores', 'instituicoes'])) {
    header("Location: listar_entidades.php?erro=" . urlencode("Parâmetros de edição inválidos."));
    exit();
}

// Define as colunas e chaves primárias conforme a tabela
if ($tabela == 'doadores') {
    $pk = 'id_doador';
    $col_nome = 'nome_razao_social';
    $col_documento = 'documento_cpf_cnpj';
    $titulo = 'Doador (Pessoa Física)';
} else { // instituicoes
    $pk = 'id_instituicao';
    $col_nome = 'nome_fantasia';
    $col_documento = 'cnpj';
    $titulo = 'Instituição Receptora';
}

// 2. Consulta PRINCIPAL: Busca os dados do registro específico
$sql_entidade = "SELECT * FROM $tabela WHERE $pk = $id";
$resultado = mysqli_query($conexao, $sql_entidade);
$entidade_atual = mysqli_fetch_assoc($resultado);

// Se o registro não for encontrado
if (!$entidade_atual) {
    header("Location: listar_entidades.php?erro=" . urlencode("Registro para edição não encontrado."));
    exit();
}

mysqli_close($conexao);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar <?php echo $titulo; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        /* Importação da Fonte */
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap');
        :root {
            --primary-color: #27ae60;
            --secondary-color: #34495e;
            --text-color: #333;
            --light-bg: #ecf0f1;
            --border-color: #bdc3c7;
            --shadow-color: rgba(0, 0, 0, 0.1);
        }

        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: var(--light-bg);
            color: var(--text-color);
            line-height: 1.6;
        }

        .container {
            max-width: 900px; /* Ajustado para formulário */
            margin: 30px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px var(--shadow-color);
        }

        h1 {
            text-align: center;
            color: var(--secondary-color);
            margin-bottom: 30px;
            font-weight: 700;
        }

        h2 {
            border-bottom: 3px solid var(--primary-color);
            padding-bottom: 12px;
            margin-top: 40px;
            color: var(--secondary-color);
            font-weight: 500;
            font-size: 1.8em;
        }
        
        /* Estilo de Navegação (mantido, mas com link de retorno) */
        .nav-links { 
            margin-bottom: 30px; 
            border-bottom: 1px solid #eee; 
            padding-bottom: 15px; 
            text-align: center;
        }
        .nav-links a { 
            margin: 0 10px; 
            background-color: var(--secondary-color);
            color: white; 
            padding: 8px 15px; 
            border-radius: 6px; 
            text-decoration: none; 
            font-weight: 500;
            transition: background-color 0.2s ease;
        }
        .nav-links a:hover { 
            background-color: #2c3e50; 
            text-decoration: none;
        }

        /* Estilo de Formulário */
        label { display: block; margin-top: 15px; margin-bottom: 5px; font-weight: 400; color: var(--secondary-color); font-size: 0.95em; }
        input[type="text"], input[type="number"], select { 
            width: 100%; 
            padding: 12px; 
            margin-bottom: 15px; 
            border: 1px solid var(--border-color); 
            border-radius: 6px; 
            box-sizing: border-box; 
            font-size: 1em; 
            transition: border-color 0.3s ease;
        }
        button { 
            background-color: var(--primary-color); 
            color: white; 
            padding: 12px 25px; 
            border: none; 
            border-radius: 6px; 
            cursor: pointer; 
            margin-top: 25px; 
            font-size: 1.1em; 
            font-weight: 700; 
            transition: background-color 0.3s ease, transform 0.2s ease; 
            display: block; width: 100%; 
        }
        button:hover { background-color: #229954; transform: translateY(-2px); }

        /* Alerta Vermelho (para exibir erro no redirecionamento) */
        .alert-danger {
            background-color: #f8d7da; 
            color: #721c24; 
            border: 1px solid #f5c6cb;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Editar Entidade <br> <small style="font-size: 0.6em; font-weight: 300; color: #7f8c8d;">Registro #<?php echo $id; ?></small></h1>

    <div class="nav-links">
        <a href="listar_entidades.php">← Voltar para a Lista</a>
    </div>
    
    <div id="feedback-message"></div>

    <h2>1. Atualizar Dados do <?php echo $titulo; ?></h2>
    
    <form action="atualizar_entidade.php" method="POST">
        
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <input type="hidden" name="tabela" value="<?php echo $tabela; ?>">

        <?php if ($tabela == 'doadores'): ?>
            <label for="tipo_doador">Tipo de Doador:</label>
            <select id="tipo_doador" name="tipo_doador" required>
                <option value="Pessoa Física" <?php if ($entidade_atual['tipo_doador'] == 'Pessoa Física') echo 'selected'; ?>>Pessoa Física</option>
                <option value="Pessoa Jurídica" <?php if ($entidade_atual['tipo_doador'] == 'Pessoa Jurídica') echo 'selected'; ?>>Pessoa Jurídica</option>
            </select>
        <?php endif; ?>

        <label for="nome">Nome / Razão Social:</label>
        <input type="text" id="nome" name="nome" required 
            value="<?php echo htmlspecialchars($entidade_atual[$col_nome]); ?>">
        
        <label for="documento">CPF / CNPJ:</label>
        <input type="text" id="documento" name="documento" required 
            value="<?php echo htmlspecialchars($entidade_atual[$col_documento]); ?>">
        
        <?php if (isset($entidade_atual['email'])): ?>
        <label for="email">E-mail:</label>
        <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($entidade_atual['email']); ?>">
        <?php endif; ?>


        <button type="submit">Salvar Alterações</button>
    </form>
</div>

<script>
    function checkFeedback() {
        const urlParams = new URLSearchParams(window.location.search);
        const errorMessage = urlParams.get('erro');
        const feedbackDiv = document.getElementById('feedback-message');

        if (errorMessage) {
            const decodedMessage = decodeURIComponent(errorMessage);
            feedbackDiv.innerHTML = `
                <div class="alert-danger">
                    ❌ Erro na Atualização: ${decodedMessage}
                </div>
            `;
            history.replaceState(null, '', window.location.pathname);
        }
    }
    document.addEventListener('DOMContentLoaded', checkFeedback);
</script>
</body>
</html>