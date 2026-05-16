<?php 
require_once 'header.php'; 

if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Ações
if (isset($_GET['mudar_tipo'])) {
    $id = intval($_GET['mudar_tipo']);
    $novo_tipo = $_GET['tipo'] === 'admin' ? 'cliente' : 'admin';
    
    // Impedir que o admin logado mude seu próprio tipo (auto-bloqueio)
    if ($id !== $_SESSION['usuario_id']) {
        $stmt = $conn->prepare("UPDATE usuarios SET tipo = ? WHERE id = ?");
        if ($stmt !== false) {
            $stmt->bind_param("si", $novo_tipo, $id);
            $stmt->execute();
        }
    }
    header("Location: admin_usuarios.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($id !== $_SESSION['usuario_id']) {
        $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
        if ($stmt !== false) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
        }
    }
    header("Location: admin_usuarios.php");
    exit();
}

$usuarios = $conn->query("SELECT id, nome, email, tipo, criado_em FROM usuarios ORDER BY criado_em DESC");
?>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>Gerenciar Usuários</h1>
        <div style="display: flex; gap: 1rem;">
            <a href="admin.php" class="btn">Produtos</a>
            <a href="index.php" class="btn">Loja</a>
        </div>
    </div>

    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Tipo</th>
                    <th>Cadastro</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $usuarios->fetch_assoc()): ?>
                <tr>
                    <td><strong><?php echo $user['nome']; ?></strong></td>
                    <td><?php echo $user['email']; ?></td>
                    <td>
                        <span class="badge <?php echo $user['tipo'] === 'admin' ? 'badge-success' : 'badge-stock'; ?>">
                            <?php echo ucfirst($user['tipo']); ?>
                        </span>
                    </td>
                    <td><?php echo date('d/m/Y', strtotime($user['criado_em'])); ?></td>
                    <td>
                        <?php if ($user['id'] !== $_SESSION['usuario_id']): ?>
                            <div style="display: flex; gap: 0.5rem;">
                                <a href="?mudar_tipo=<?php echo $user['id']; ?>&tipo=<?php echo $user['tipo']; ?>" 
                                   class="btn btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">
                                   Tornar <?php echo $user['tipo'] === 'admin' ? 'Cliente' : 'Admin'; ?>
                                </a>
                                <a href="?delete=<?php echo $user['id']; ?>" 
                                   class="btn btn-danger" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;" 
                                   onclick="return confirm('Excluir este usuário permanentemente?')">Excluir</a>
                            </div>
                        <?php else: ?>
                            <span style="font-size: 0.8rem; color: #666;">(Você)</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'footer.php'; ?>
