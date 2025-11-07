<?php
session_start();
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: login.php");
    exit();
}
require_once 'conexao.php';

$sql_select = "
    SELECT 
        d.id_doacao, 
        doad.nome_razao_social AS nome_doador, 
        ali.nome_alimento,
        ali.unidade_medida, 
        d.quantidade,
        inst.nome_fantasia AS nome_instituicao, 
        d.data_hora_doacao
    FROM doacoes d
    JOIN doadores doad ON d.fk_id_doador = doad.id_doador
    JOIN alimentos ali ON d.fk_id_alimento = ali.id_alimento
    JOIN instituicoes inst ON d.fk_id_instituicao = inst.id_instituicao
    ORDER BY d.data_hora_doacao DESC;
";

$resultado = mysqli_query($conexao, $sql_select);
$doacoes = [];

if ($resultado && mysqli_num_rows($resultado) > 0) {
    while($row = mysqli_fetch_assoc($resultado)) {
        $doacoes[] = $row;
    }
}

$mensagem_sucesso = "";
if (isset($_GET['sucesso']) && $_GET['sucesso'] == 'true') {
    // Captura o sucesso da doação ou edição
    $mensagem_sucesso = "<div class='alert-success'>✅ Transação concluída e estoque atualizado com sucesso!</div>";
}


mysqli_close($conexao);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Doações</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
    /* Importação da Fonte */
    @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap');
    
    /* Variáveis de Cor */
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
        <h1>Consulta de Doações <br> <small style="font-size: 0.6em; font-weight: 300; color: #7f8c8d;">Registro e Histórico de Transações</small></h1>

        <div class="nav-links">
            <a href="index.php">Cadastro de Entidade</a>
            <a href="listar_entidades.php">Listar Entidades</a>
            <a href="registrar_doacao_form.php">Registrar Doação</a>
            <a href="listar_doacoes.php">Consultar Doações</a>
        </div>
        
        <?php echo $mensagem_sucesso; ?>

        <h2>Doações Realizadas</h2>
        <?php if (!empty($doacoes)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Doador</th>
                        <th>Alimento</th>
                        <th>Quantidade</th>
                        <th>Instituição Receptora</th>
                        <th>Data/Hora</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($doacoes as $doacao): ?>
                        <tr>
                            <td><?php echo $doacao['id_doacao']; ?></td>
                            <td><?php echo htmlspecialchars($doacao['nome_doador']); ?></td>
                            <td><?php echo htmlspecialchars($doacao['nome_alimento']); ?></td>
                            <td><?php echo htmlspecialchars($doacao['quantidade'] . ' ' . $doacao['unidade_medida']); ?></td>
                            <td><?php echo htmlspecialchars($doacao['nome_instituicao']); ?></td>
                            <td><?php echo date('d/m/Y H:i:s', strtotime($doacao['data_hora_doacao'])); ?></td>
                            
                            <td>
                                <a href="editar_doacao.php?id=<?php echo $doacao['id_doacao']; ?>" 
                                   style="color: #007bff; text-decoration: none; font-weight: bold; font-size: 1.1em;"
                                   title="Editar Doação #<?php echo $doacao['id_doacao']; ?>">
                                    &#x270E; Editar 
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="color: #c0392b; font-weight: bold;">Nenhuma doação registrada ainda.</p>
        <?php endif; ?>
    </div>
</body>
</html>