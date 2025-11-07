<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_digitado = $_POST['usuario'] ?? '';
    $senha_digitada = $_POST['senha'] ?? '';
    
    // DEFINIÇÃO DO USUÁRIO E SENHA DO MVP (FIXO)
    $usuario_valido = 'admin';
    $senha_valida = '12345'; // Em projetos reais, use password_hash()!

    // Verifica credenciais
    if ($usuario_digitado === $usuario_valido && $senha_digitada === $senha_valida) {
        
        // Credenciais válidas: inicia a sessão
        $_SESSION['logado'] = true;
        $_SESSION['usuario'] = $usuario_valido;
        
        // Redireciona para a página principal (index.php)
        header("Location: index.php");
        exit();
    } else {
        // Credenciais inválidas: volta para o login com erro
        header("Location: login.php?erro=1");
        exit();
    }
} else {
    // Acesso direto, redireciona para o login
    header("Location: login.php");
    exit();
}