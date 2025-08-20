<?php

session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: index.html');
    exit();
}

if ($_SESSION['usuario']['nivel'] !== 'Administrador') {
    header('Location: dashboard.php'); 
    exit();
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Cadastro de Usuário - Sistema JyM</title>
<link rel="stylesheet" href="assets/css/style-login.css">
</head>
<body>
<div class="login-container" id="app-cadastro">
    <img src="assets/img/logo.png" alt="Logo JyM" class="logo">
    <h2>Cadastro de Usuário</h2>
    <form @submit.prevent="cadastrarUsuario">
        <input type="text" v-model="nome" placeholder="Nome Completo" required>
        <input type="text" v-model="usuario" placeholder="Nome de Usuário (para login)" required>
        <input type="email" v-model="email" placeholder="Email (Opcional)">
        <input type="password" v-model="senha" placeholder="Senha" required>
        <select v-model="nivel" required>
            <option value="">Selecione o nível de acesso</option>
            <option value="Funcionario">Funcionário</option>
            <option value="Administrador">Administrador</option>
        </select>
        <button type="submit">Cadastrar</button>
    </form>
    <p><a href="index.html">Voltar ao Login</a></p>
    <div v-if="mensagemErro" class="mensagem-erro">{{ mensagemErro }}</div>
    <div v-if="mensagemSucesso" class="mensagem-sucesso">{{ mensagemSucesso }}</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
<script src="assets/js/cadastro.js"></script>
</body>
</html>