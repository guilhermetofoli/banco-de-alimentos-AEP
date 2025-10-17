<?php
require_once 'conexao.php';

// 1. Buscar todos os doadores para o <select>
$sql_doadores = "SELECT id_doador, nome_razao_social FROM doadores ORDER BY nome_razao_social ASC";
$doadores = mysqli_query($conexao, $sql_doadores);

// 2. Buscar todos os alimentos para o <select>
$sql_alimentos = "SELECT id_alimento, nome_alimento, unidade_medida FROM alimentos ORDER BY nome_alimento ASC";
$alimentos = mysqli_query($conexao, $sql_alimentos);

// 3. Buscar todas as instituições para o <select>
$sql_instituicoes = "SELECT id_instituicao, nome_fantasia FROM instituicoes ORDER BY nome_fantasia ASC";
$instituicoes = mysqli_query($conexao, $sql_instituicoes);

// Fechamos a conexão antes de montar o HTML se tudo deu certo
if (!$doadores || !$alimentos || !$instituicoes) {
    die("Erro ao carregar dados para o formulário: " . mysqli_error($conexao));
}

mysqli_close($conexao);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Doação</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
   <style>
    /* Importação da Fonte */
    @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap');
    
    /* Variáveis de Cor */
    :root {
        --primary-color: #27ae60; /* Verde vibrante */
        --secondary-color: #34495e; /* Azul escuro */
        --text-color: #333;
        --light-bg: #ecf0f1; /* Cinza claro */
        --border-color: #bdc3c7; /* Cinza médio */
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
        max-width: 1200px; /* Mais largo para as listas */
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
    
    /* Estilo de Navegação */
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
    input[type="text"]:focus, input[type="number"]:focus, select:focus { border-color: var(--primary-color); outline: none; box-shadow: 0 0 5px rgba(39, 174, 96, 0.3); }
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

    /* Estilo de Tabela */
    table { 
        width: 100%; 
        border-collapse: collapse; 
        margin-top: 25px; 
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    th, td {
        text-align: left;
        padding: 12px 15px;
        border-bottom: 1px solid #ddd;
    }
    th {
        background-color: var(--secondary-color);
        color: white;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.9em;
    }
    tr:nth-child(even) { background-color: #f9f9f9; }
    tr:hover { background-color: #f1f1f1; }
    
    /* Alertas e Mensagens */
    .alert-success { 
        background-color: #d4edda; 
        color: #155724; 
        border: 1px solid #c3e6cb; 
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
    <h1>Registro de Doação <br> <small style="font-size: 0.6em; font-weight: 300; color: #7f8c8d;">Transação de Alimentos</small></h1>
    
    <div class="nav-links">
        <a href="index.html">Cadastro de Entidade</a>
        <a href="listar_entidades.php">Listar Entidades</a>
        <a href="registrar_doacao_form.php">Registrar Doação</a>
        <a href="listar_doacoes.php">Consultar Doações</a>
    </div>

    <h2>1. Informações da Doação</h2>
    <form action="processar_doacao.php" method="POST">
        
        <label for="fk_id_doador">Doador:</label>
        <select id="fk_id_doador" name="fk_id_doador" required>
            <option value="">Selecione o Doador...</option>
            <?php while($doador = mysqli_fetch_assoc($doadores)): ?>
                <option value="<?php echo $doador['id_doador']; ?>">
                    <?php echo htmlspecialchars($doador['nome_razao_social']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="fk_id_alimento">Alimento:</label>
        <select id="fk_id_alimento" name="fk_id_alimento" required>
            <option value="">Selecione o Alimento...</option>
            <?php while($alimento = mysqli_fetch_assoc($alimentos)): ?>
                <option value="<?php echo $alimento['id_alimento']; ?>">
                    <?php echo htmlspecialchars($alimento['nome_alimento'] . ' (' . $alimento['unidade_medida'] . ')'); ?>
                </option>
            <?php endwhile; ?>
        </select>
        
        <label for="quantidade">Quantidade (em Unidades/Kg/Litro):</label>
        <input type="number" id="quantidade" name="quantidade" step="0.01" min="0.01" required placeholder="Ex: 50.50 ou 10">

        <label for="fk_id_instituicao">Instituição Beneficiada (Receptora):</label>
        <select id="fk_id_instituicao" name="fk_id_instituicao" required>
            <option value="">Selecione a Instituição...</option>
            <?php while($instituicao = mysqli_fetch_assoc($instituicoes)): ?>
                <option value="<?php echo $instituicao['id_instituicao']; ?>">
                    <?php echo htmlspecialchars($instituicao['nome_fantasia']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <button type="submit">Registrar Doação</button>
    </form>
</div>
</body>
</html>