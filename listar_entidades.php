<?php
session_start();
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: login.php");
    exit();
}
require_once 'conexao.php';

$sql_doadores = "SELECT id_doador, nome_razao_social, documento_cpf_cnpj FROM doadores ORDER BY nome_razao_social ASC";
$res_doadores = mysqli_query($conexao, $sql_doadores);

$sql_inst = "SELECT id_instituicao, nome_fantasia, cnpj FROM instituicoes ORDER BY nome_fantasia ASC";
$res_inst = mysqli_query($conexao, $sql_inst);

$mensagem_sucesso = "";
$mensagem_erro = "";

if (isset($_GET['sucesso']) && $_GET['sucesso'] == 'true') {
    if (isset($_GET['msg'])) {
        $msg = htmlspecialchars($_GET['msg']);
        $mensagem_sucesso = "<div class='alert-success'>✅ $msg</div>";
    } elseif (isset($_GET['entidade'])) { 
        $entidade = htmlspecialchars($_GET['entidade']);
        $mensagem_sucesso = "<div class='alert-success'>✅ $entidade cadastrado(a) com sucesso!</div>";
    }
}
if (isset($_GET['erro'])) {
    $erro_msg = htmlspecialchars($_GET['erro']);
    $mensagem_erro = "<div class='alert-danger'>❌ Erro: $erro_msg</div>";
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
        max-width: 1200px;
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
            <a href="index.php">Cadastro de Entidade</a>
            <a href="listar_entidades.php">Listar Entidades</a>
            <a href="registrar_doacao_form.php">Registrar Doação</a>
            <a href="listar_doacoes.php">Consultar Doações</a>
        </div>
        
        <?php echo $mensagem_erro; ?>
        <?php echo $mensagem_sucesso; ?>

        <h2>Doadores (Pessoas Físicas)</h2>
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Documento</th>
                    <th>Ações</th> 
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($res_doadores) > 0): ?>
                    <?php while($doador = mysqli_fetch_assoc($res_doadores)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($doador['nome_razao_social']); ?></td>
                            <td><?php echo htmlspecialchars($doador['documento_cpf_cnpj']); ?></td>
                            <td>
                                <a href="editar_entidade.php?tabela=doadores&id=<?php echo $doador['id_doador']; ?>" title="Editar Doador">&#x270E; Editar</a>
                                &nbsp;|&nbsp;
                                <a href="deletar_entidade.php?tabela=doadores&id=<?php echo $doador['id_doador']; ?>" 
                                   onclick="return confirm('Confirmar exclusão? Isso pode remover registros relacionados.')"
                                   style="color: red;" title="Excluir Doador">
                                    &#x2715; Excluir
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="3">Nenhum doador cadastrado.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <h2>Instituições Receptoras</h2>
        <table>
            <thead>
                <tr>
                    <th>Nome Fantasia</th>
                    <th>CNPJ</th>
                    <th>Ações</th> 
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($res_inst) > 0): ?>
                    <?php while($instituicao = mysqli_fetch_assoc($res_inst)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($instituicao['nome_fantasia']); ?></td>
                            <td><?php echo htmlspecialchars($instituicao['cnpj']); ?></td>
                            <td>
                                <a href="editar_entidade.php?tabela=instituicoes&id=<?php echo $instituicao['id_instituicao']; ?>" title="Editar Instituição">&#x270E; Editar</a>
                                &nbsp;|&nbsp;
                                <a href="deletar_entidade.php?tabela=instituicoes&id=<?php echo $instituicao['id_instituicao']; ?>" 
                                   onclick="return confirm('Confirmar exclusão?')"
                                   style="color: red;" title="Excluir Instituição">
                                    &#x2715; Excluir
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="3">Nenhuma instituição cadastrada.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>