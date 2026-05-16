<?php
require_once 'config.php';

if (isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

$erro = "";
$sucesso = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];

    if ($senha !== $confirmar_senha) {
        $erro = "As senhas não coincidem!";
    } else {
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        if ($stmt === false) {
            $erro = "Erro ao preparar a query: " . $conn->error;
        } else {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                $erro = "Este e-mail já está cadastrado!";
            } else {
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, 'cliente')");
                if ($stmt === false) {
                    $erro = "Erro ao preparar a query: " . $conn->error;
                } else {
                    $stmt->bind_param("sss", $nome, $email, $senha_hash);
                    if ($stmt->execute()) {
                        $sucesso = "Cadastro realizado com sucesso! <a href='login.php'>Clique aqui para entrar.</a>";
                    } else {
                        $erro = "Erro ao cadastrar usuário.";
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro - TechStore</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="login-body">
    <button id="theme-toggle" class="theme-toggle" title="Alternar Tema" style="position: fixed; top: 20px; right: 20px; z-index: 1000;">
        <i class="fas fa-moon"></i>
    </button>
    <div class="login-container">
        <form method="post" class="login-form">
            <h1>Criar Conta</h1>
            <?php if ($erro): ?>
                <div class="alert alert-danger"><?php echo $erro; ?></div>
            <?php endif; ?>
            <?php if ($sucesso): ?>
                <div class="alert alert-success" style="background: #dcfce7; color: #166534; border: 1px solid #bbf7d0;"><?php echo $sucesso; ?></div>
            <?php endif; ?>
            <div class="form-group">
                <label>Nome Completo</label>
                <input type="text" name="nome" required>
            </div>
            <div class="form-group">
                <label>E-mail</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Senha</label>
                <input type="password" name="senha" required>
            </div>
            <div class="form-group">
                <label>Confirmar Senha</label>
                <input type="password" name="confirmar_senha" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Cadastrar</button>
            <p style="margin-top: 1rem; text-align: center;">
                Já tem conta? <a href="login.php">Faça login</a>
            </p>
        </form>
    </div>
    <script>
        // Aplicar tema dark se estiver salvo
        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark-theme');
        }

        const themeToggle = document.getElementById('theme-toggle');
        const icon = themeToggle.querySelector('i');

        function updateIcon() {
            if (document.body.classList.contains('dark-theme')) {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            } else {
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
            }
        }

        updateIcon();

        themeToggle.addEventListener('click', () => {
            document.body.classList.toggle('dark-theme');
            const isDark = document.body.classList.contains('dark-theme');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            updateIcon();
        });
    </script>
</body>
</html>
