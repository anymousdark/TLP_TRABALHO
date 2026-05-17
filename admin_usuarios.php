<?php 
require_once 'header.php'; 

$animais = '<div class="animal-corner animal-corner-br">' . animal_elefante(70) . '</div>';
$animais .= '<div class="animal-corner animal-corner-tl">' . animal_macaco(55) . '</div>';

if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$erro = "";
$sucesso = "";

// Editar usuario (admin)
if (isset($_POST['editar_usuario'])) {
    $id = intval($_POST['id']);
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);

    if (empty($nome) || empty($email)) {
        $erro = "Preencha todos os campos.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "E-mail invalido.";
    } else {
        $check = $conn->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
        $check->bind_param("si", $email, $id);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $erro = "E-mail ja esta em uso por outro usuario.";
        } else {
            $stmt = $conn->prepare("UPDATE usuarios SET nome = ?, email = ? WHERE id = ?");
            $stmt->bind_param("ssi", $nome, $email, $id);
            if ($stmt->execute()) {
                $sucesso = "Usuario atualizado!";
            } else {
                $erro = "Erro ao atualizar.";
            }
        }
    }
}

// Resetar senha (admin)
if (isset($_GET['reset_senha'])) {
    $id = intval($_GET['reset_senha']);
    $nova_senha = password_hash('123456', PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
    $stmt->bind_param("si", $nova_senha, $id);
    if ($stmt->execute()) {
        $sucesso = "Senha resetada para: 123456";
    }
    header("Location: admin_usuarios.php?ok=1");
    exit();
}

// Mudar tipo
if (isset($_GET['mudar_tipo'])) {
    $id = intval($_GET['mudar_tipo']);
    if ($id !== $_SESSION['usuario_id']) {
        $tipo = $_GET['tipo'] === 'admin' ? 'cliente' : 'admin';
        $stmt = $conn->prepare("UPDATE usuarios SET tipo = ? WHERE id = ?");
        $stmt->bind_param("si", $tipo, $id);
        $stmt->execute();
    }
    header("Location: admin_usuarios.php");
    exit();
}

// Excluir
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($id !== $_SESSION['usuario_id']) {
        $conn->query("UPDATE pedidos SET usuario_id = NULL WHERE usuario_id = $id");
        $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
    header("Location: admin_usuarios.php");
    exit();
}

$edit_user = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $edit_user = $stmt->get_result()->fetch_assoc();
}

$usuarios = $conn->query("
    SELECT u.id, u.nome, u.email, u.tipo, u.criado_em,
           (SELECT COUNT(*) FROM pedidos WHERE usuario_id = u.id) as total_pedidos
    FROM usuarios u
    ORDER BY u.criado_em DESC
");
?>
<style>
.modal-overlay {
    position: fixed; inset: 0; z-index: 1000;
    background: rgba(0,0,0,0.3);
    backdrop-filter: blur(8px);
    display: flex; align-items: center; justify-content: center;
}
.modal-box {
    background: var(--card);
    padding: 2rem;
    border-radius: var(--radius-lg);
    width: 100%; max-width: 420px;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--border);
    backdrop-filter: blur(var(--glass-blur)) saturate(180%);
}
</style>

<div class="container">
    <div class="flex-between" style="margin-bottom: 2rem;">
        <h1>Gerenciar Usuarios</h1>
        <div class="flex gap-md">
            <a href="admin.php" class="btn btn-secondary">Produtos</a>
            <a href="index.php" class="btn btn-success">Loja</a>
        </div>
    </div>

    <?php if ($erro): ?>
        <div class="alert alert-danger"><?php echo $erro; ?></div>
    <?php elseif ($sucesso): ?>
        <div class="alert alert-success"><?php echo $sucesso; ?></div>
    <?php elseif (isset($_GET['ok'])): ?>
        <div class="alert alert-success">Senha resetada para: 123456</div>
    <?php endif; ?>

    <!-- Estatisticas -->
    <?php
    $stats = $conn->query("SELECT
        (SELECT COUNT(*) FROM usuarios) as total,
        (SELECT COUNT(*) FROM usuarios WHERE tipo='admin') as admins,
        (SELECT COUNT(*) FROM usuarios WHERE tipo='cliente') as clientes
    ")->fetch_assoc();
    ?>
    <div class="stats-grid" style="margin-bottom: 2rem;">
        <div class="stat-card glass" style="background: linear-gradient(135deg, #0071e3, #0060c9); color: white;">
            <div class="stat-value"><?php echo $stats['total']; ?></div>
            <div class="stat-label">Total de Usuarios</div>
        </div>
        <div class="stat-card glass" style="background: linear-gradient(135deg, #34c759, #28a745); color: white;">
            <div class="stat-value"><?php echo $stats['clientes']; ?></div>
            <div class="stat-label">Clientes</div>
        </div>
        <div class="stat-card glass" style="background: linear-gradient(135deg, #f5a623, #d97706); color: white;">
            <div class="stat-value"><?php echo $stats['admins']; ?></div>
            <div class="stat-label">Administradores</div>
        </div>
    </div>

    <!-- Lista de Usuarios -->
    <div class="section-card" style="padding: 0; overflow: hidden;">
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Tipo</th>
                    <th style="text-align: center;">Pedidos</th>
                    <th>Cadastro</th>
                    <th style="text-align: center;">Acoes</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $usuarios->fetch_assoc()): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($user['nome']); ?></strong></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td>
                        <span class="badge <?php echo $user['tipo'] === 'admin' ? 'badge-success' : 'badge-stock'; ?>">
                            <?php echo ucfirst($user['tipo']); ?>
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <span class="badge badge-stock"><?php echo $user['total_pedidos']; ?></span>
                    </td>
                    <td><?php echo date('d/m/Y', strtotime($user['criado_em'])); ?></td>
                    <td style="text-align: center;">
                        <?php if ($user['id'] !== $_SESSION['usuario_id']): ?>
                            <div class="flex gap-sm" style="justify-content: center; flex-wrap: wrap;">
                                <a href="?edit=<?php echo $user['id']; ?>" class="btn btn-primary" style="padding: 0.3rem 0.7rem; font-size: 0.8rem;">Editar</a>
                                <a href="?mudar_tipo=<?php echo $user['id']; ?>&tipo=<?php echo $user['tipo']; ?>" 
                                   class="btn btn-secondary" style="padding: 0.3rem 0.7rem; font-size: 0.8rem;">
                                   Tornar <?php echo $user['tipo'] === 'admin' ? 'Cliente' : 'Admin'; ?>
                                </a>
                                <a href="?reset_senha=<?php echo $user['id']; ?>" 
                                   class="btn btn-secondary" style="padding: 0.3rem 0.7rem; font-size: 0.8rem; background: #f5a623;"
                                   onclick="return confirm('Resetar senha para 123456?')">Resetar Senha</a>
                                <a href="?delete=<?php echo $user['id']; ?>" 
                                   class="btn btn-danger" style="padding: 0.3rem 0.7rem; font-size: 0.8rem;" 
                                   onclick="return confirm('Excluir este usuario? Os pedidos serao mantidos.')">Excluir</a>
                            </div>
                        <?php else: ?>
                            <span class="text-muted" style="font-size: 0.8rem;">(Voce)</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal de Edicao -->
<?php if ($edit_user): ?>
<div class="modal-overlay" onclick="if(event.target===this)window.location='admin_usuarios.php'">
    <div class="modal-box" onclick="event.stopPropagation()">
        <div class="flex-between" style="margin-bottom: 1.5rem;">
            <h2 style="margin: 0;">Editar Usuario</h2>
            <a href="admin_usuarios.php" style="color: var(--text-light); font-size: 1.5rem; text-decoration: none;">&times;</a>
        </div>
        <form method="post" class="form-grid">
            <input type="hidden" name="id" value="<?php echo $edit_user['id']; ?>">
            <div class="form-group">
                <label>Nome</label>
                <input type="text" name="nome" value="<?php echo htmlspecialchars($edit_user['nome']); ?>" required>
            </div>
            <div class="form-group">
                <label>E-mail</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($edit_user['email']); ?>" required>
            </div>
            <div class="flex gap-md" style="margin-top: 0.5rem;">
                <button type="submit" name="editar_usuario" class="btn btn-primary" style="flex: 1;">Salvar</button>
                <a href="admin_usuarios.php" class="btn btn-secondary" style="flex: 1; text-align: center;">Cancelar</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php echo $animais; ?>
<?php require_once 'footer.php'; ?>
