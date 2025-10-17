<?php
require_once 'conexao.php'; 

// 1. Consulta Doadores
$sql_doadores = "SELECT nome_razao_social, documento_cpf_cnpj FROM doadores ORDER BY nome_razao_social ASC";
$res_doadores = mysqli_query($conexao, $sql_doadores);

// 2. Consulta Instituições
$sql_inst = "SELECT nome_fantasia, cnpj FROM instituicoes ORDER BY nome_fantasia ASC";
$res_inst = mysqli_query($conexao, $sql_inst);

// 3. Verifica Mensagem de Sucesso
$mensagem_sucesso = "";
if (isset($_GET['sucesso']) && $_GET['sucesso'] == 'true' && isset($_GET['entidade'])) {
    $entidade = htmlspecialchars($_GET['entidade']);
    $mensagem_sucesso = "<div class='alert-success'> Novo $entidade cadastrado com sucesso!</div>";
}

mysqli_close($conexao);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Entidades - Banco de Alimentos</title>
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

    /* Estilo de Formulário*/
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

    /* Estilo de Tabela*/
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
        <h1>Listagem de Entidades <br> <small style="font-size: 0.6em; font-weight: 300; color: #7f8c8d;">Doadores e Instituições</small></h1>

        <div class="nav-links">
            <a href="index.html">Cadastro de Entidade</a>
            <a href="listar_entidades.php">Listar Entidades</a>
            <a href="registrar_doacao_form.php">Registrar Doação</a>
            <a href="listar_doacoes.php">Consultar Doações</a>
        </div>
        
        <?php echo $mensagem_sucesso; ?>

        <h2>Doadores (Pessoas Físicas)</h2>
        <table>
            <thead><tr><th>Nome</th><th>Documento</th></tr></thead>
            <tbody>
                <?php if (mysqli_num_rows($res_doadores) > 0): ?>
                    <?php while($doador = mysqli_fetch_assoc($res_doadores)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($doador['nome_razao_social']); ?></td>
                            <td><?php echo htmlspecialchars($doador['documento_cpf_cnpj']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="2">Nenhum doador cadastrado.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <h2>Instituições Receptoras</h2>
        <table>
            <thead><tr><th>Nome Fantasia</th><th>CNPJ</th></tr></thead>
            <tbody>
                <?php if (mysqli_num_rows($res_inst) > 0): ?>
                    <?php while($instituicao = mysqli_fetch_assoc($res_inst)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($instituicao['nome_fantasia']); ?></td>
                            <td><?php echo htmlspecialchars($instituicao['cnpj']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="2">Nenhuma instituição cadastrada.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>