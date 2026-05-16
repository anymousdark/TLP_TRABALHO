<?php 
require_once 'header.php'; 

// Proteção: Apenas admin pode acessar
if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Ações CRUD
if (isset($_POST['salvar'])) {
    $id = intval($_POST['id']);
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $preco = $_POST['preco'];
    $estoque = $_POST['estoque'];
    $imagem_atual = $_POST['imagem_atual'] ?? '';
    $imagem_nova = $imagem_atual;

    // Upload de Imagem
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
        $extensao = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $novo_nome = uniqid() . "." . $extensao;
        $destino = "uploads/" . $novo_nome;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {
            $imagem_nova = $destino;
            // Opcional: deletar imagem antiga se existir e não for URL externa
            if ($imagem_atual && file_exists($imagem_atual) && strpos($imagem_atual, 'http') === false) {
                unlink($imagem_atual);
            }
        }
    }

    if ($id > 0) {
        $stmt = $conn->prepare("UPDATE produtos SET nome=?, descricao=?, preco=?, estoque=?, imagem=? WHERE id=?");
        if ($stmt === false) {
            die("Erro ao preparar UPDATE: " . $conn->error);
        }
        $stmt->bind_param("ssdisi", $nome, $descricao, $preco, $estoque, $imagem_nova, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO produtos (nome, descricao, preco, estoque, imagem) VALUES (?, ?, ?, ?, ?)");
        if ($stmt === false) {
            die("Erro ao preparar INSERT: " . $conn->error);
        }
        $stmt->bind_param("ssdis", $nome, $descricao, $preco, $estoque, $imagem_nova);
    }
    $stmt->execute();
    header("Location: admin.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // Deletar imagem antes de remover do BD
    $stmt = $conn->prepare("SELECT imagem FROM produtos WHERE id = ?");
    if ($stmt !== false) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($p = $res->fetch_assoc()) {
            if ($p['imagem'] && file_exists($p['imagem']) && strpos($p['imagem'], 'http') === false) {
                unlink($p['imagem']);
            }
        }
    }

    $stmt = $conn->prepare("DELETE FROM produtos WHERE id = ?");
    if ($stmt !== false) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
    header("Location: admin.php");
    exit();
}

$edit_prod = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM produtos WHERE id = ?");
    if ($stmt !== false) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $edit_prod = $stmt->get_result()->fetch_assoc();
    }
}

$produtos = $conn->query("SELECT * FROM produtos ORDER BY id DESC");
?>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>📊 Painel Administrativo</h1>
        <div style="display: flex; gap: 0.5rem;">
            <a href="admin_usuarios.php" class="btn" style="background: #64748b;">👥 Usuários</a>
            <a href="index.php" class="btn" style="background: #22c55e;">🏪 Loja</a>
        </div>
    </div>

    <!-- Estatísticas -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <?php 
        // Estatísticas - proteger contra falhas de query (evita fetch_assoc() em false)
        $total_prod = 0;
        $estoque_total = 0;
        $valor_total = 0;
        $pedidos_total = 0;

        $q_total = $conn->query("SELECT COUNT(*) as c FROM produtos");
        if ($q_total !== false) {
            $row = $q_total->fetch_assoc();
            $total_prod = intval($row['c'] ?? 0);
        }

        $q_estoque = $conn->query("SELECT SUM(estoque) as e FROM produtos");
        if ($q_estoque !== false) {
            $row = $q_estoque->fetch_assoc();
            $estoque_total = intval($row['e'] ?? 0);
        }

        $q_valor = $conn->query("SELECT SUM(preco * estoque) as v FROM produtos");
        if ($q_valor !== false) {
            $row = $q_valor->fetch_assoc();
            $valor_total = $row['v'] ?? 0;
        }

        $q_pedidos = $conn->query("SELECT COUNT(*) as p FROM pedidos");
        if ($q_pedidos !== false) {
            $row = $q_pedidos->fetch_assoc();
            $pedidos_total = intval($row['p'] ?? 0);
        }
        ?>
        <div style="background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; padding: 1.5rem; border-radius: 0.75rem; text-align: center;">
            <div style="font-size: 2rem; font-weight: bold;"><?php echo $total_prod; ?></div>
            <div style="font-size: 0.9rem; opacity: 0.9;">Produtos Cadastrados</div>
        </div>
        <div style="background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white; padding: 1.5rem; border-radius: 0.75rem; text-align: center;">
            <div style="font-size: 2rem; font-weight: bold;"><?php echo $estoque_total; ?></div>
            <div style="font-size: 0.9rem; opacity: 0.9;">Itens em Estoque</div>
        </div>
        <div style="background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 1.5rem; border-radius: 0.75rem; text-align: center;">
            <div style="font-size: 2rem; font-weight: bold;">R$ <?php echo number_format($valor_total, 0, ',', '.'); ?></div>
            <div style="font-size: 0.9rem; opacity: 0.9;">Valor Total em Estoque</div>
        </div>
        <div style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white; padding: 1.5rem; border-radius: 0.75rem; text-align: center;">
            <div style="font-size: 2rem; font-weight: bold;"><?php echo $pedidos_total; ?></div>
            <div style="font-size: 0.9rem; opacity: 0.9;">Pedidos Totais</div>
        </div>
    </div>

    <!-- Formulário de Novo/Editar Produto -->
    <div style="margin-bottom: 3rem; background: var(--card); padding: 2rem; border-radius: 1rem; box-shadow: var(--shadow); border: 1px solid var(--border);">
        <h2 style="margin-bottom: 1.5rem;">📝 <?php echo $edit_prod ? '✏️ Editar Produto' : '➕ Novo Produto'; ?></h2>
        <form method="post" enctype="multipart/form-data" style="display: grid; gap: 1rem;">
            <input type="hidden" name="id" value="<?php echo $edit_prod['id'] ?? 0; ?>">
            <input type="hidden" name="imagem_atual" value="<?php echo $edit_prod['imagem'] ?? ''; ?>">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label>🏷️ Nome do Produto <span style="color: red;">*</span></label>
                    <input type="text" name="nome" value="<?php echo htmlspecialchars($edit_prod['nome'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label>💰 Preço (R$) <span style="color: red;">*</span></label>
                    <input type="number" step="0.01" name="preco" value="<?php echo $edit_prod['preco'] ?? ''; ?>" required>
                </div>
            </div>
            
            <div class="form-group">
                <label>📄 Descrição</label>
                <textarea name="descricao" rows="4" style="resize: vertical;"><?php echo htmlspecialchars($edit_prod['descricao'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-group">
                <label>📦 Quantidade em Estoque <span style="color: red;">*</span></label>
                <input type="number" name="estoque" value="<?php echo $edit_prod['estoque'] ?? ''; ?>" required min="0">
            </div>
            
            <div class="form-group">
                <label>🖼️ Foto do Produto</label>
                <input type="file" name="foto" accept="image/*" <?php echo $edit_prod ? '' : 'required'; ?>>
                <?php if ($edit_prod && $edit_prod['imagem']): ?>
                    <div style="margin-top: 0.5rem; display: flex; align-items: center; gap: 1rem;">
                        <img src="<?php echo $edit_prod['imagem']; ?>" width="80" height="80" style="object-fit: cover; border-radius: 0.5rem; border: 2px solid var(--border);">
                        <div style="font-size: 0.85rem; color: var(--text-light);">📸 Imagem atual<br>Upload nova para substituir</div>
                    </div>
                <?php endif; ?>
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                <button type="submit" name="salvar" class="btn btn-primary" style="flex: 1;">
                    💾 <?php echo $edit_prod ? 'Atualizar' : 'Criar'; ?> Produto
                </button>
                <?php if ($edit_prod): ?>
                    <a href="admin.php" class="btn" style="background: #6c757d; color: white; flex: 1; text-align: center;">❌ Cancelar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Busca e Filtros -->
    <div style="background: var(--card); padding: 1.5rem; border-radius: 1rem; box-shadow: var(--shadow); border: 1px solid var(--border); margin-bottom: 2rem;">
        <h3>🔍 Buscar Produtos</h3>
        <form method="get" style="display: flex; gap: 1rem;">
            <input type="text" name="buscar" placeholder="Nome do produto..." style="flex: 1; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem;" value="<?php echo htmlspecialchars($_GET['buscar'] ?? ''); ?>">
            <button type="submit" class="btn btn-primary">Buscar</button>
            <?php if (isset($_GET['buscar'])): ?>
                <a href="admin.php" class="btn" style="background: #6c757d; color: white;">Limpar</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Tabela de Produtos -->
    <h2 style="margin-bottom: 1rem;">📋 Catálogo de Produtos (<?php echo $produtos->num_rows; ?> itens)</h2>
    <div style="overflow-x: auto; background: var(--card); border-radius: 1rem; box-shadow: var(--shadow); border: 1px solid var(--border);">
        <table style="width: 100%;">
            <thead>
                <tr>
                    <th style="text-align: left;">🖼️ Foto</th>
                    <th style="text-align: left;">📝 Nome</th>
                    <th style="text-align: left;">📄 Descrição</th>
                    <th style="text-align: right;">💰 Preço</th>
                    <th style="text-align: center;">📦 Estoque</th>
                    <th style="text-align: center;">⚙️ Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $produtos->fetch_assoc()): ?>
                <tr style="border-top: 1px solid var(--border);">
                    <td style="padding: 1rem; vertical-align: middle;">
                        <img src="<?php echo htmlspecialchars($row['imagem']); ?>" width="60" height="60" style="object-fit: cover; border-radius: 0.5rem; border: 1px solid var(--border);">
                    </td>
                    <td style="padding: 1rem;"><strong><?php echo htmlspecialchars($row['nome']); ?></strong></td>
                    <td style="padding: 1rem; max-width: 200px; overflow: hidden; text-overflow: ellipsis;" title="<?php echo htmlspecialchars($row['descricao']); ?>"><?php echo htmlspecialchars(substr($row['descricao'], 0, 50)); ?>...</td>
                    <td style="padding: 1rem; text-align: right;"><strong>R$ <?php echo number_format($row['preco'], 2, ',', '.'); ?></strong></td>
                    <td style="padding: 1rem; text-align: center;">
                        <span class="badge <?php echo $row['estoque'] > 0 ? 'badge-stock' : 'badge-out'; ?>">
                            <?php echo $row['estoque']; ?> un
                        </span>
                    </td>
                    <td style="padding: 1rem; text-align: center;">
                        <div style="display: flex; gap: 0.5rem; justify-content: center;">
                            <a href="?edit=<?php echo $row['id']; ?>" class="btn btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.85rem; white-space: nowrap;">✏️ Editar</a>
                            <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger" style="padding: 0.4rem 0.8rem; font-size: 0.85rem; white-space: nowrap;" onclick="return confirm('⚠️ Tem certeza que deseja excluir este produto?')">🗑️ Excluir</a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php if ($produtos->num_rows == 0): ?>
        <div style="text-align: center; padding: 2rem; color: var(--text-light);">
            <p style="font-size: 1.1rem;">📭 Nenhum produto encontrado</p>
        </div>
        <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>
