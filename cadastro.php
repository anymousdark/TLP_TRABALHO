<?php
require_once 'config.php';
require_once 'animais.php';

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
        $erro = "As senhas nao coincidem!";
    } else {
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        if ($stmt === false) {
            $erro = "Erro ao preparar a query: " . $conn->error;
        } else {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                $erro = "Este e-mail ja esta cadastrado!";
            } else {
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, 'cliente')");
                if ($stmt === false) {
                    $erro = "Erro ao preparar a query: " . $conn->error;
                } else {
                    $stmt->bind_param("sss", $nome, $email, $senha_hash);
                    if ($stmt->execute()) {
                        $sucesso = "Cadastro realizado! <a href='login.php'>Faca login</a>";
                    } else {
                        $erro = "Erro ao cadastrar usuario.";
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-ao">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Lakastech</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <script>
        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark-theme');
        }
    </script>

    <div class="auth-page">
        <!-- Painel Ilustrado -->
        <div class="auth-hero">
            <div class="auth-hero-content">
                <div class="mascot-scene">
                    <!-- Boneco Principal (acenando) -->
                    <div class="mascot">
                        <svg viewBox="0 0 200 240" class="mascot-svg">
                            <ellipse cx="100" cy="175" rx="40" ry="50" fill="var(--success)" opacity="0.9"/>
                            <circle cx="100" cy="90" r="45" fill="var(--success)" opacity="0.9"/>
                            <!-- Olhos -->
                            <circle cx="83" cy="82" r="6" fill="white"/>
                            <circle cx="117" cy="82" r="6" fill="white"/>
                            <circle cx="84" cy="81" r="3" fill="var(--text)"/>
                            <circle cx="118" cy="81" r="3" fill="var(--text)"/>
                            <!-- Sorriso grande -->
                            <path d="M82 102 Q100 120 118 102" stroke="white" stroke-width="3" fill="none" stroke-linecap="round"/>
                            <!-- Bracos (acenando) -->
                            <path class="arm-left" d="M60 160 L35 130 L28 138" stroke="var(--success)" stroke-width="10" fill="none" stroke-linecap="round" stroke-linejoin="round" opacity="0.9"/>
                            <path class="arm-right wave" d="M140 160 L165 125 L175 115" stroke="var(--success)" stroke-width="10" fill="none" stroke-linecap="round" stroke-linejoin="round" opacity="0.9"/>
                            <!-- Estrela na mao -->
                            <text x="178" y="108" font-size="18" fill="var(--accent)">✦</text>
                            <!-- Orelhas antena -->
                            <rect x="60" y="55" width="8" height="12" rx="4" fill="var(--accent)"/>
                            <rect x="132" y="55" width="8" height="12" rx="4" fill="var(--accent)"/>
                            <!-- Pernas -->
                            <rect x="78" y="220" width="14" height="20" rx="5" fill="var(--success)" opacity="0.8"/>
                            <rect x="108" y="220" width="14" height="20" rx="5" fill="var(--success)" opacity="0.8"/>
                        </svg>
                    </div>

                    <!-- Elementos Flutuantes -->
                    <div class="float-icon icon-1" style="--delay: 0s;">
                        <i class="fas fa-gift"></i>
                    </div>
                    <div class="float-icon icon-2" style="--delay: 1s;">
                        <i class="fas fa-tag"></i>
                    </div>
                    <div class="float-icon icon-3" style="--delay: 2s;">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="float-icon icon-4" style="--delay: 0.5s;">
                        <i class="fas fa-smile"></i>
                    </div>
                    <div class="float-icon icon-5" style="--delay: 1.5s;">
                        <i class="fas fa-box"></i>
                    </div>

                    <!-- Particulas -->
                    <div class="particle p1"></div>
                    <div class="particle p2"></div>
                    <div class="particle p3"></div>
                    <div class="particle p4"></div>
                    <div class="particle p5"></div>
                    <div class="particle p6"></div>
                </div>

                <div class="auth-tagline">
                    <h2>Junte-se a nos!</h2>
                    <p>Crie sua conta e aproveite as melhores ofertas</p>
                </div>
            </div>
        </div>

        <!-- Painel do Formulario -->
        <div class="auth-panel">
            <div class="auth-panel-inner">
                <a href="index.php" class="auth-logo">Lakastech<span>.</span></a>
                <form method="post">
                    <h1>Criar Conta</h1>
                    <p class="auth-subtitle">Preencha seus dados</p>

                    <?php if ($erro): ?>
                        <div class="alert alert-danger"><?php echo $erro; ?></div>
                    <?php endif; ?>
                    <?php if ($sucesso): ?>
                        <div class="alert alert-success"><?php echo $sucesso; ?></div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label>Nome Completo</label>
                        <input type="text" name="nome" required placeholder="Seu nome">
                    </div>
                    <div class="form-group">
                        <label>E-mail</label>
                        <input type="email" name="email" required placeholder="seu@email.com">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Senha</label>
                            <input type="password" name="senha" required placeholder="Min. 6 caracteres" minlength="6">
                        </div>
                        <div class="form-group">
                            <label>Confirmar</label>
                            <input type="password" name="confirmar_senha" required placeholder="Repita a senha" minlength="6">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Cadastrar</button>

                    <p class="auth-footer-text">
                        Ja tem conta? <a href="login.php">Faca login</a>
                    </p>
                </form>
            </div>
            <div class="theme-toggle-wrap">
                <button id="theme-toggle" class="theme-toggle" title="Alternar Tema">
                    <i class="fas fa-moon"></i>
                </button>
            </div>
        </div>
    </div>

    <script>
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
