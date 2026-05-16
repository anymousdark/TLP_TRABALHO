<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechStore Enterprise</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <script>
        // Aplicar tema imediatamente para evitar flash de cor clara
        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark-theme');
        }
    </script>

    <header class="navbar">
        <div class="navbar-content">
            <a href="index.php" class="logo">TechStore<span>.</span></a>
            
            <div class="nav-links">
                <a href="index.php">Catálogo</a>
                <a href="carrinho.php">Carrinho (<?php echo count($_SESSION['carrinho'] ?? []); ?>)</a>
                
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <a href="pedidos.php">Meus Pedidos</a>
                    <?php if ($_SESSION['usuario_tipo'] === 'admin'): ?>
                        <div style="position: relative; display: inline-block;" class="admin-dropdown">
                            <a href="#" style="color: var(--primary); font-weight: bold;">Admin <i class="fas fa-chevron-down" style="font-size: 0.7rem;"></i></a>
                            <div class="dropdown-content">
                                <a href="admin.php">Produtos</a>
                                <a href="admin_usuarios.php">Usuários</a>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="user-info">
                        <span>Olá, <strong><?php echo $_SESSION['usuario_nome']; ?></strong></span>
                        <a href="logout.php" class="btn-sm">Sair</a>
                    </div>
                <?php else: ?>
                    <a href="login.php">Entrar</a>
                    <a href="cadastro.php" class="btn btn-primary" style="padding: 0.4rem 1rem;">Criar Conta</a>
                <?php endif; ?>

                <button id="theme-toggle" class="theme-toggle" title="Alternar Tema">
                    <i class="fas fa-moon"></i>
                </button>
            </div>
        </div>
    </header>

    <style>
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: var(--card);
            min-width: 160px;
            box-shadow: var(--shadow);
            z-index: 1;
            border-radius: 0.5rem;
            border: 1px solid var(--border);
            top: 100%;
        }
        .dropdown-content a {
            color: var(--text);
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }
        .dropdown-content a:hover { background-color: var(--background); }
        .admin-dropdown:hover .dropdown-content { display: block; }
    </style>
