<?php
$servidor = "localhost";
$usuario = "root";
$senha = "";
$banco = "banco_de_alimentos"; 

$conexao = mysqli_connect($servidor, $usuario, $senha, $banco);

if (mysqli_connect_errno()) {
    die("Falha na conexão com o banco de dados: " . mysqli_connect_error());
}
mysqli_set_charset($conexao, "utf8");
?>