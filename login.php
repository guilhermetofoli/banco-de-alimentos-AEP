<?php
session_start();
// Se o usuário JÁ estiver logado, redireciona para a página principal
if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
    header("Location: index.php");
    exit();
}

$erro_login = "";
if (isset($_GET['erro'])) {
    $erro_login = "Usuário ou senha inválidos. Tente novamente.";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Banco de Alimentos</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        /* (CSS de Estrutura Simples para o Login) */
        :root { --primary-color: #27ae60; --secondary-color: #34495e; --light-bg: #ecf0f1; }
        body { font-family: 'Roboto', sans-serif; background-color: var(--light-bg); display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .login-container { width: 350px; padding: 40px; background: white; border-radius: 8px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); }
        h1 { text-align: center; color: var(--secondary-color); margin-bottom: 30px; font-weight: 700; font-size: 1.8em; }
        label { display: block; margin-top: 15px; margin-bottom: 5px; font-weight: 500; }
        input[type="text"], input[type="password"] { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { background-color: var(--primary-color); color: white; padding: 12px; border: none; border-radius: 4px; cursor: pointer; margin-top: 20px; width: 100%; font-size: 1em; font-weight: 700; transition: background-color 0.3s; }
        button:hover { background-color: #229954; }
        .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; margin-top: 15px; border-radius: 4px; text-align: center; }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Acesso ao Painel</h1>
        
        <?php if ($erro_login): ?>
            <div class="alert-danger"><?php echo $erro_login; ?></div>
        <?php endif; ?>

        <form action="auth.php" method="POST">
            <label for="usuario">Usuário:</label>
            <input type="text" id="usuario" name="usuario" required>

            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required>

            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>