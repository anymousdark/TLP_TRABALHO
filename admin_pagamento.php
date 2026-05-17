<?php 
require_once 'header.php'; 

$animais = '<div class="animal-corner animal-corner-bl">' . animal_elefante(70) . '</div>';
$animais .= '<div class="animal-corner animal-corner-br">' . animal_girafa(65) . '</div>';

if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// CRUD
if (isset($_POST['salvar'])) {
    $id = intval($_POST['id']);
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $conta = $_POST['conta'];
    $titular = $_POST['titular'];
    $ativo = isset($_POST['ativo']) ? 1 : 0;

    if ($id > 0) {
        $stmt = $conn->prepare("UPDATE metodos_pagamento SET nome=?, descricao=?, conta=?, titular=?, ativo=? WHERE id=?");
        $stmt->bind_param("ssssii", $nome, $descricao, $conta, $titular, $ativo, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO metodos_pagamento (nome, descricao, conta, titular, ativo) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $nome, $descricao, $conta, $titular, $ativo);
    }
    $stmt->execute();
    header("Location: admin_pagamento.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM metodos_pagamento WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: admin_pagamento.php");
    exit();
}

$edit_metodo = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM metodos_pagamento WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $edit_metodo = $stmt->get_result()->fetch_assoc();
}

$metodos = $conn->query("SELECT * FROM metodos_pagamento ORDER BY id ASC");
?>

<div class="container" style="max-width: 800px;">
    <div class="flex-between" style="margin-bottom: 2rem;">
        <div>
            <h1 style="margin-bottom: 0.25rem;">Metodos de Pagamento</h1>
            <p style="color: var(--text-light); font-size: 0.9rem;">Gerir as contas para transferencia da loja</p>
        </div>
        <a href="admin.php" class="btn btn-secondary">Voltar</a>
    </div>

    <!-- Form -->
    <div class="section-card">
        <h3><?php echo $edit_metodo ? 'Editar Metodo' : 'Novo Metodo'; ?></h3>
        <form method="post" class="form-grid">
            <input type="hidden" name="id" value="<?php echo $edit_metodo['id'] ?? 0; ?>">

            <div class="form-row">
                <div class="form-group">
                    <label>Nome *</label>
                    <input type="text" name="nome" value="<?php echo htmlspecialchars($edit_metodo['nome'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label>Descricao</label>
                    <input type="text" name="descricao" value="<?php echo htmlspecialchars($edit_metodo['descricao'] ?? ''); ?>" placeholder="Ex: Transferencia via...">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Conta / IBAN *</label>
                    <input type="text" name="conta" value="<?php echo htmlspecialchars($edit_metodo['conta'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label>Titular *</label>
                    <input type="text" name="titular" value="<?php echo htmlspecialchars($edit_metodo['titular'] ?? ''); ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label class="flex gap-sm" style="align-items: center;">
                    <input type="checkbox" name="ativo" value="1" <?php echo !isset($edit_metodo) || $edit_metodo['ativo'] ? 'checked' : ''; ?>>
                    Metodo activo (visivel na finalizacao da compra)
                </label>
            </div>

            <div class="flex gap-sm">
                <button type="submit" name="salvar" class="btn btn-primary"><?php echo $edit_metodo ? 'Salvar Alteracoes' : 'Adicionar Metodo'; ?></button>
                <?php if ($edit_metodo): ?>
                    <a href="admin_pagamento.php" class="btn btn-secondary">Cancelar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Lista -->
    <h2>Metodos Cadastrados (<?php echo $metodos->num_rows; ?>)</h2>
    <?php if ($metodos->num_rows == 0): ?>
        <p>Nenhum metodo cadastrado.</p>
    <?php else: ?>
        <?php while ($m = $metodos->fetch_assoc()): ?>
        <div class="section-card" style="padding: 1.25rem; margin-bottom: 1rem;">
            <div style="display: flex; justify-content: space-between; align-items: start; flex-wrap: wrap; gap: 0.75rem;">
                <div style="flex: 1;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <strong style="font-size: 1.05rem;"><?php echo htmlspecialchars($m['nome']); ?></strong>
                        <?php if (!$m['ativo']): ?>
                            <span class="badge badge-out">Inactivo</span>
                        <?php endif; ?>
                    </div>
                    <?php if ($m['descricao']): ?>
                        <div style="font-size: 0.85rem; color: var(--text-light); margin-bottom: 0.25rem;"><?php echo htmlspecialchars($m['descricao']); ?></div>
                    <?php endif; ?>
                    <div style="font-size: 0.9rem;">
                        <span style="color: var(--text-light);">Conta:</span> <strong><?php echo htmlspecialchars($m['conta']); ?></strong>
                    </div>
                    <div style="font-size: 0.9rem;">
                        <span style="color: var(--text-light);">Titular:</span> <strong><?php echo htmlspecialchars($m['titular']); ?></strong>
                    </div>
                </div>
                <div class="flex gap-sm">
                    <a href="?edit=<?php echo $m['id']; ?>" class="btn btn-primary" style="padding: 0.35rem 0.8rem; font-size: 0.8rem;">Editar</a>
                    <a href="?delete=<?php echo $m['id']; ?>" class="btn btn-danger" style="padding: 0.35rem 0.8rem; font-size: 0.8rem;" onclick="return confirm('Eliminar <?php echo htmlspecialchars($m['nome']); ?>?')">Eliminar</a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>

<?php echo $animais; ?>
<?php require_once 'footer.php'; ?>
