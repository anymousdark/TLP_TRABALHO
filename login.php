<?php
require_once 'config.php';
require_once 'animais.php';

if (isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

$erro = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $stmt = $conn->prepare("SELECT id, nome, senha, tipo FROM usuarios WHERE email = ?");
    if ($stmt === false) {
        die("Erro ao preparar a query: " . $conn->error);
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($senha, $user['senha'])) {
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['usuario_nome'] = $user['nome'];
            $_SESSION['usuario_tipo'] = $user['tipo'];
            header("Location: index.php");
            exit();
        } else {
            $erro = "Senha incorreta!";
        }
    } else {
        $erro = "E-mail nao encontrado!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-ao">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Lakastech</title>
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
                    <!-- Boneco Principal -->
                    <div class="mascot">
                        <svg viewBox="0 0 200 240" class="mascot-svg">
                            <!-- Corpo -->
                            <ellipse cx="100" cy="175" rx="40" ry="50" fill="var(--primary)" opacity="0.9"/>
                            <!-- Cabeca -->
                            <circle cx="100" cy="90" r="45" fill="var(--primary)" opacity="0.9"/>
                            <!-- Olhos -->
                            <circle cx="83" cy="82" r="6" fill="white"/>
                            <circle cx="117" cy="82" r="6" fill="white"/>
                            <circle cx="84" cy="81" r="3" fill="var(--text)"/>
                            <circle cx="118" cy="81" r="3" fill="var(--text)"/>
                            <!-- Sorriso -->
                            <path d="M85 100 Q100 115 115 100" stroke="white" stroke-width="3" fill="none" stroke-linecap="round"/>
                            <!-- Bracos -->
                            <path class="arm-left" d="M60 160 L35 130 L28 138" stroke="var(--primary)" stroke-width="10" fill="none" stroke-linecap="round" stroke-linejoin="round" opacity="0.9"/>
                            <path class="arm-right" d="M140 160 L165 130 L172 138" stroke="var(--primary)" stroke-width="10" fill="none" stroke-linecap="round" stroke-linejoin="round" opacity="0.9"/>
                            <!-- Orelhas antena -->
                            <rect x="60" y="55" width="8" height="12" rx="4" fill="var(--accent)"/>
                            <rect x="132" y="55" width="8" height="12" rx="4" fill="var(--accent)"/>
                            <!-- Pernas -->
                            <rect x="78" y="220" width="14" height="20" rx="5" fill="var(--primary)" opacity="0.8"/>
                            <rect x="108" y="220" width="14" height="20" rx="5" fill="var(--primary)" opacity="0.8"/>
                        </svg>
                    </div>

                    <!-- Elementos Flutuantes -->
                    <div class="float-icon icon-1">
                        <i class="fas fa-laptop-code"></i>
                    </div>
                    <div class="float-icon icon-2">
                        <i class="fas fa-cart-plus"></i>
                    </div>
                    <div class="float-icon icon-3">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="float-icon icon-4">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <div class="float-icon icon-5">
                        <i class="fas fa-cog"></i>
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
                    <h2>Bem-vindo de volta!</h2>
                    <p>Entre na melhor loja de tecnologia</p>
                </div>
            </div>
        </div>

        <!-- Painel do Formulario -->
        <div class="auth-panel">
            <div class="auth-panel-inner">
                <a href="index.php" class="auth-logo">Lakastech<span>.</span></a>
                <form method="post">
                    <h1>Entrar</h1>
                    <p class="auth-subtitle">Acesse sua conta</p>

                    <?php if ($erro): ?>
                        <div class="alert alert-danger"><?php echo $erro; ?></div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label>E-mail</label>
                        <input type="email" name="email" required placeholder="seu@email.com">
                    </div>
                    <div class="form-group">
                        <label>Senha</label>
                        <input type="password" name="senha" required placeholder="Sua senha">
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Entrar</button>

                    <p class="auth-footer-text">
                        Novo por aqui? <a href="cadastro.php">Crie sua conta</a>
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
