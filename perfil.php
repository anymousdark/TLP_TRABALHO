<?php
require_once 'header.php';

$animais = '<div class="animal-corner animal-corner-tr">' . animal_girafa(65) . '</div>';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$erro = "";
$sucesso = "";
$tab = $_GET['tab'] ?? 'dados';

// Garantir que a coluna pagamento_info existe
$col_check = $conn->query("SHOW COLUMNS FROM usuarios LIKE 'pagamento_info'");
if ($col_check && $col_check->num_rows === 0) {
    $conn->query("ALTER TABLE usuarios ADD COLUMN pagamento_info TEXT DEFAULT NULL");
}

$stmt = $conn->prepare("SELECT id, nome, email, tipo, criado_em, COALESCE(pagamento_info, '{}') as pagamento_info FROM usuarios WHERE id = ?");
if ($stmt === false) {
    die("Erro na consulta: " . $conn->error);
}
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();

if (!$usuario) {
    header("Location: logout.php");
    exit();
}

$pagamento = json_decode($usuario['pagamento_info'] ?? '{}', true);

// Atualizar dados pessoais
if (isset($_POST['atualizar_perfil'])) {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);

    if (empty($nome) || empty($email)) {
        $erro = "Preencha todos os campos.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "E-mail invalido.";
    } else {
        $check = $conn->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
        $check->bind_param("si", $email, $usuario_id);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $erro = "Este e-mail ja esta em uso.";
        } else {
            $stmt = $conn->prepare("UPDATE usuarios SET nome = ?, email = ? WHERE id = ?");
            $stmt->bind_param("ssi", $nome, $email, $usuario_id);
            if ($stmt->execute()) {
                $_SESSION['usuario_nome'] = $nome;
                $sucesso = "Perfil atualizado!";
                $usuario['nome'] = $nome;
                $usuario['email'] = $email;
            } else {
                $erro = "Erro ao atualizar perfil.";
            }
        }
    }
}

// Alterar senha
if (isset($_POST['alterar_senha'])) {
    $senha_atual = $_POST['senha_atual'];
    $nova_senha = $_POST['nova_senha'];
    $confirmar = $_POST['confirmar_senha'];

    $stmt = $conn->prepare("SELECT senha FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    if (!password_verify($senha_atual, $row['senha'])) {
        $erro = "Senha atual incorreta.";
    } elseif (strlen($nova_senha) < 6) {
        $erro = "Nova senha deve ter no minimo 6 caracteres.";
    } elseif ($nova_senha !== $confirmar) {
        $erro = "Confirmacao nao coincide.";
    } else {
        $hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
        $stmt->bind_param("si", $hash, $usuario_id);
        if ($stmt->execute()) {
            $sucesso = "Senha alterada!";
        } else {
            $erro = "Erro ao alterar senha.";
        }
    }
}

// Salvar dados de pagamento
if (isset($_POST['salvar_pagamento'])) {
    $dados = [
        'multicaixa' => $_POST['multicaixa'] ?? '',
        'bfa' => $_POST['bfa'] ?? '',
        'bai' => $_POST['bai'] ?? '',
        'preferencia' => $_POST['preferencia'] ?? ''
    ];
    $json = json_encode($dados, JSON_UNESCAPED_UNICODE);
    $stmt = $conn->prepare("UPDATE usuarios SET pagamento_info = ? WHERE id = ?");
    $stmt->bind_param("si", $json, $usuario_id);
    if ($stmt->execute()) {
        $sucesso = "Dados de pagamento salvos!";
        $pagamento = $dados;
    } else {
        $erro = "Erro ao salvar.";
    }
}

// Eliminar dados de pagamento
if (isset($_POST['eliminar_pagamento'])) {
    $stmt = $conn->prepare("UPDATE usuarios SET pagamento_info = NULL WHERE id = ?");
    $stmt->bind_param("i", $usuario_id);
    if ($stmt->execute()) {
        $sucesso = "Dados de pagamento eliminados!";
        $pagamento = [];
    } else {
        $erro = "Erro ao eliminar.";
    }
}
?>

<style>
.tabs {
    display: inline-flex;
    gap: 0.25rem;
    margin-bottom: 2rem;
    background: var(--card);
    border-radius: var(--radius-md);
    padding: 0.25rem;
    border: 1px solid var(--border);
}
.tab {
    padding: 0.75rem 1.5rem;
    text-align: center;
    border-radius: var(--radius-sm);
    text-decoration: none;
    color: var(--text-light);
    font-weight: 500;
    font-size: 0.9rem;
    transition: all 0.2s;
    white-space: nowrap;
}
.tab:hover { color: var(--text); background: var(--border); }
.tab.active {
    background: var(--primary);
    color: white;
}
.tabs-wrap {
    text-align: center;
}


</style>

<div class="container" style="max-width: 700px;">
    <h1>Meu Perfil</h1>

    <?php if ($erro): ?>
        <div class="alert alert-danger"><?php echo $erro; ?></div>
    <?php elseif ($sucesso): ?>
        <div class="alert alert-success"><?php echo $sucesso; ?></div>
    <?php endif; ?>

    <!-- Badge do tipo -->
    <div class="section-card" style="padding: 1rem 1.5rem;">
        <div class="flex-between">
            <span class="badge <?php echo $usuario['tipo'] === 'admin' ? 'badge-success' : 'badge-stock'; ?>">
                <?php echo ucfirst($usuario['tipo']); ?>
            </span>
            <span class="text-muted" style="font-size: 0.85rem;">
                Membro desde <?php echo date('d/m/Y', strtotime($usuario['criado_em'])); ?>
            </span>
        </div>
    </div>

    <!-- Abas -->
    <div class="tabs-wrap">
        <div class="tabs">
            <a href="?tab=dados" class="tab <?php echo $tab === 'dados' ? 'active' : ''; ?>">
                <i class="fas fa-user"></i> Dados Pessoais
            </a>
            <a href="?tab=pagamento" class="tab <?php echo $tab === 'pagamento' ? 'active' : ''; ?>">
                <i class="fas fa-credit-card"></i> Pagamento
            </a>
        </div>
    </div>

    <?php if ($tab === 'dados'): ?>
    <!-- Aba Dados Pessoais -->
    <div class="section-card">
        <h2>Dados Pessoais</h2>
        <form method="post" class="form-grid">
            <div class="form-group">
                <label>Nome</label>
                <input type="text" name="nome" value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>
            </div>
            <div class="form-group">
                <label>E-mail</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
            </div>
            <button type="submit" name="atualizar_perfil" class="btn btn-primary">Salvar Alteracoes</button>
        </form>
    </div>

    <div class="section-card">
        <h2>Alterar Senha</h2>
        <form method="post" class="form-grid">
            <div class="form-group">
                <label>Senha Atual</label>
                <input type="password" name="senha_atual" required minlength="6">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Nova Senha</label>
                    <input type="password" name="nova_senha" required minlength="6">
                </div>
                <div class="form-group">
                    <label>Confirmar Nova Senha</label>
                    <input type="password" name="confirmar_senha" required minlength="6">
                </div>
            </div>
            <button type="submit" name="alterar_senha" class="btn btn-primary">Alterar Senha</button>
        </form>
    </div>

    <div style="text-align: center;">
        <a href="pedidos.php" class="btn btn-secondary">Ver Meus Pedidos</a>
    </div>

    <?php elseif ($tab === 'pagamento'): ?>
    <!-- Aba Metodos de Pagamento -->
    <div class="section-card">
        <h2 style="margin-bottom: 1rem;">Os Meus Dados de Pagamento</h2>
        <p class="text-muted" style="margin-top: -0.5rem; margin-bottom: 1.25rem;">
            Informe os seus dados para agilizar as compras
        </p>

        <form method="post" class="form-grid">
            <div class="form-group">
                <label><i class="fas fa-mobile-alt"></i> Multicaixa Express (seu numero)</label>
                <input type="text" name="multicaixa" placeholder="--- --- ---"
                       value="<?php echo htmlspecialchars($pagamento['multicaixa'] ?? ''); ?>">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-university"></i> BFA Net - N° de Conta</label>
                    <input type="text" name="bfa" placeholder="N° de conta BFA"
                           value="<?php echo htmlspecialchars($pagamento['bfa'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-university"></i> BAI Directo - N° de Conta</label>
                    <input type="text" name="bai" placeholder="N° de conta BAI"
                           value="<?php echo htmlspecialchars($pagamento['bai'] ?? ''); ?>">
                </div>
            </div>
            <div class="form-group">
                <label>Metodo Preferido</label>
                <select name="preferencia">
                    <option value="">Selecionar...</option>
                    <option value="multicaixa" <?php echo ($pagamento['preferencia'] ?? '') === 'multicaixa' ? 'selected' : ''; ?>>Multicaixa Express</option>
                    <option value="bfa" <?php echo ($pagamento['preferencia'] ?? '') === 'bfa' ? 'selected' : ''; ?>>BFA Net</option>
                    <option value="bai" <?php echo ($pagamento['preferencia'] ?? '') === 'bai' ? 'selected' : ''; ?>>BAI Directo</option>
                </select>
            </div>
            <div class="flex gap-sm" style="margin-top: 0.5rem;">
                <button type="submit" name="salvar_pagamento" class="btn btn-primary" style="flex: 1;">Salvar Dados</button>
                <?php if (!empty(array_filter($pagamento))): ?>
                    <button type="submit" name="eliminar_pagamento" class="btn btn-danger" onclick="return confirm('Eliminar todos os dados de pagamento?')">Eliminar</button>
                <?php endif; ?>
            </div>
        </form>
    </div>
    <?php endif; ?>
</div>



<?php echo $animais; ?>
<?php require_once 'footer.php'; ?>
