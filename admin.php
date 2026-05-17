<?php 
require_once 'header.php'; 

$animais = '<div class="animal-corner animal-corner-bl">' . animal_rinoceronte(75) . '</div>';
$animais .= '<div class="animal-corner animal-corner-tr">' . animal_leao(65) . '</div>';

if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Acoes CRUD
if (isset($_POST['salvar'])) {
    $id = intval($_POST['id']);
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $preco = $_POST['preco'];
    $estoque = $_POST['estoque'];
    $categoria = $_POST['categoria'] ?? '';
    $imagem_atual = $_POST['imagem_atual'] ?? '';
    $imagem_nova = $imagem_atual;

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
        $extensao = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $novo_nome = uniqid() . "." . $extensao;
        $destino = "uploads/" . $novo_nome;
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {
            $imagem_nova = $destino;
            if ($imagem_atual && file_exists($imagem_atual) && strpos($imagem_atual, 'http') === false) {
                unlink($imagem_atual);
            }
        }
    }

    if ($id > 0) {
        $stmt = $conn->prepare("UPDATE produtos SET nome=?, descricao=?, preco=?, estoque=?, imagem=?, categoria=? WHERE id=?");
        $stmt->bind_param("ssdissi", $nome, $descricao, $preco, $estoque, $imagem_nova, $categoria, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO produtos (nome, descricao, preco, estoque, imagem, categoria) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdiss", $nome, $descricao, $preco, $estoque, $imagem_nova, $categoria);
    }
    $stmt->execute();
    header("Location: admin.php");
    exit();
}

// Atualizar estoque rapido via AJAX
if (isset($_POST['quick_estoque'])) {
    $id = intval($_POST['id']);
    $qtd = intval($_POST['qtd']);
    $stmt = $conn->prepare("UPDATE produtos SET estoque = ? WHERE id = ?");
    $stmt->bind_param("ii", $qtd, $id);
    $stmt->execute();
    header("Location: admin.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
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
    $stmt = $conn->prepare("DELETE FROM itens_pedido WHERE produto_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt = $conn->prepare("DELETE FROM produtos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: admin.php");
    exit();
}

$edit_prod = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM produtos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $edit_prod = $stmt->get_result()->fetch_assoc();
}

// Filtro de busca
$buscar = $_GET['buscar'] ?? '';
$ordenar = $_GET['ordenar'] ?? 'id';
$direcao = $_GET['direcao'] ?? 'DESC';

$ordens_validas = ['id', 'nome', 'preco', 'estoque', 'categoria'];
if (!in_array($ordenar, $ordens_validas)) $ordenar = 'id';
$direcao = strtoupper($direcao) === 'ASC' ? 'ASC' : 'DESC';

if ($buscar) {
    $busca_like = "%$buscar%";
    $stmt = $conn->prepare("SELECT * FROM produtos WHERE nome LIKE ? OR descricao LIKE ? ORDER BY $ordenar $direcao");
    $stmt->bind_param("ss", $busca_like, $busca_like);
    $stmt->execute();
    $produtos = $stmt->get_result();
} else {
    $produtos = $conn->query("SELECT * FROM produtos ORDER BY $ordenar $direcao");
}

// Stats
$total_prod = intval(($conn->query("SELECT COUNT(*) as c FROM produtos")->fetch_assoc())['c'] ?? 0);
$estoque_total = intval(($conn->query("SELECT SUM(estoque) as e FROM produtos")->fetch_assoc())['e'] ?? 0);
$valor_total = ($conn->query("SELECT SUM(preco * estoque) as v FROM produtos")->fetch_assoc())['v'] ?? 0;
$pedidos_total = intval(($conn->query("SELECT COUNT(*) as p FROM pedidos")->fetch_assoc())['p'] ?? 0);
$pendentes = intval(($conn->query("SELECT COUNT(*) as p FROM pedidos WHERE status='pendente'")->fetch_assoc())['p'] ?? 0);

// Categorias existentes
$cats = $conn->query("SELECT DISTINCT categoria FROM produtos WHERE categoria IS NOT NULL AND categoria != '' ORDER BY categoria");
$categorias = [];
while ($c = $cats->fetch_assoc()) $categorias[] = $c['categoria'];
?>

<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}
.admin-layout {
    display: grid;
    grid-template-columns: 360px 1fr;
    gap: 1.5rem;
    align-items: start;
}
@media (max-width: 900px) {
    .admin-layout { grid-template-columns: 1fr; }
}
.form-sidebar { position: sticky; top: 1rem; }
.prod-card {
    display: flex;
    gap: 1rem;
    padding: 1rem;
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    transition: all 0.15s;
    margin-bottom: 0.75rem;
    align-items: center;
}
.prod-card:hover { border-color: var(--primary); box-shadow: var(--shadow); }
.prod-card .thumb {
    width: 56px;
    height: 56px;
    border-radius: var(--radius-sm);
    object-fit: cover;
    flex-shrink: 0;
    background: var(--background);
}
.prod-card .info { flex: 1; min-width: 0; }
.prod-card .info .nome { font-weight: 600; font-size: 0.95rem; display: block; }
.prod-card .info .cat { font-size: 0.75rem; color: var(--text-light); display: block; margin-top: 0.1rem; }
.prod-card .preco { font-weight: 700; font-size: 1rem; white-space: nowrap; }
.prod-card .estoque-wrap { display: flex; align-items: center; gap: 0.35rem; }
.prod-card .estoque-wrap input {
    width: 52px;
    padding: 0.2rem 0.3rem;
    font-size: 0.8rem;
    text-align: center;
    border-radius: var(--radius-sm);
}
.prod-card .acoes { display: flex; gap: 0.35rem; }
.toolbar {
    display: flex;
    gap: 0.75rem;
    align-items: center;
    flex-wrap: wrap;
    margin-bottom: 1rem;
}
.toolbar select, .toolbar input { padding: 0.45rem 0.7rem; font-size: 0.85rem; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--card); color: var(--text); }
.toolbar input { flex: 1; min-width: 140px; }
.img-preview-wrap { position: relative; display: inline-block; }
.img-preview-wrap img { border-radius: var(--radius-sm); border: 1px solid var(--border); }
.category-tag {
    display: inline-block;
    padding: 0.15rem 0.5rem;
    font-size: 0.7rem;
    border-radius: 999px;
    background: rgba(0, 113, 227, 0.1);
    color: var(--primary);
    font-weight: 500;
}
.btn-icon {
    padding: 0.35rem 0.55rem;
    font-size: 0.8rem;
    border-radius: var(--radius-sm);
    border: 1px solid var(--border);
    background: var(--card);
    color: var(--text);
    cursor: pointer;
    transition: all 0.15s;
    line-height: 1;
}
.btn-icon:hover { border-color: var(--primary); color: var(--primary); }
.btn-icon.danger:hover { border-color: var(--danger); color: var(--danger); }
.empty-admin { text-align: center; padding: 3rem 1rem; color: var(--text-light); }
.empty-admin i { font-size: 2.5rem; display: block; margin-bottom: 0.75rem; }
</style>

<div class="container" style="max-width: 1100px;">
    <div class="flex-between" style="margin-bottom: 2rem;">
        <div>
            <h1 style="margin-bottom: 0.25rem;">Produtos</h1>
            <p style="color: var(--text-light); font-size: 0.9rem;">Gira o catalogo da loja</p>
        </div>
        <div class="flex gap-sm">
            <a href="admin_pedidos.php" class="btn btn-secondary">Pedidos <?php if ($pendentes > 0): ?><span class="badge badge-pendente" style="margin-left: 0.3rem;"><?php echo $pendentes; ?></span><?php endif; ?></a>
            <a href="admin_usuarios.php" class="btn btn-secondary">Usuarios</a>
            <a href="index.php" class="btn btn-success">Ver Loja</a>
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card glass" style="background: linear-gradient(135deg, #0071e3, #0060c9); color: white;">
            <div class="stat-value"><?php echo $total_prod; ?></div>
            <div class="stat-label">Produtos</div>
        </div>
        <div class="stat-card glass" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white;">
            <div class="stat-value"><?php echo $estoque_total; ?></div>
            <div class="stat-label">Itens em Estoque</div>
        </div>
        <div class="stat-card glass" style="background: linear-gradient(135deg, #34c759, #28a745); color: white;">
            <div class="stat-value">Kz <?php echo number_format($valor_total, 0, ',', '.'); ?></div>
            <div class="stat-label">Valor em Estoque</div>
        </div>
        <div class="stat-card glass" style="background: linear-gradient(135deg, #f5a623, #d97706); color: white;">
            <div class="stat-value"><?php echo $pedidos_total; ?></div>
            <div class="stat-label">Pedidos</div>
        </div>
    </div>

    <div class="admin-layout">
        <!-- Sidebar: Formulario -->
        <div class="form-sidebar">
            <div class="section-card" style="padding: 1.25rem;">
                <h3 style="font-size: 1rem; margin-bottom: 1rem;">
                    <?php echo $edit_prod ? '<i class="fas fa-pen"></i> Editar Produto' : '<i class="fas fa-plus-circle"></i> Novo Produto'; ?>
                </h3>
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $edit_prod['id'] ?? 0; ?>">
                    <input type="hidden" name="imagem_atual" value="<?php echo $edit_prod['imagem'] ?? ''; ?>">

                    <div class="form-group" style="margin-bottom: 0.75rem;">
                        <label style="font-size: 0.8rem; font-weight: 500;">Nome *</label>
                        <input type="text" name="nome" value="<?php echo htmlspecialchars($edit_prod['nome'] ?? ''); ?>" required style="font-size: 0.85rem; padding: 0.45rem 0.6rem;">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 0.75rem;">
                        <div class="form-group">
                            <label style="font-size: 0.8rem; font-weight: 500;">Preco (Kz) *</label>
                            <input type="number" step="0.01" name="preco" value="<?php echo $edit_prod['preco'] ?? ''; ?>" required style="font-size: 0.85rem; padding: 0.45rem 0.6rem;">
                        </div>
                        <div class="form-group">
                            <label style="font-size: 0.8rem; font-weight: 500;">Estoque *</label>
                            <input type="number" name="estoque" value="<?php echo $edit_prod['estoque'] ?? ''; ?>" required min="0" style="font-size: 0.85rem; padding: 0.45rem 0.6rem;">
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 0.75rem;">
                        <label style="font-size: 0.8rem; font-weight: 500;">Categoria</label>
                        <input type="text" name="categoria" list="cat-list" value="<?php echo htmlspecialchars($edit_prod['categoria'] ?? ''); ?>" placeholder="Ex: Eletronicos" style="font-size: 0.85rem; padding: 0.45rem 0.6rem;">
                        <datalist id="cat-list">
                            <?php foreach ($categorias as $c): ?>
                            <option value="<?php echo htmlspecialchars($c); ?>">
                            <?php endforeach; ?>
                        </datalist>
                    </div>

                    <div class="form-group" style="margin-bottom: 0.75rem;">
                        <label style="font-size: 0.8rem; font-weight: 500;">Descricao</label>
                        <textarea name="descricao" rows="3" style="font-size: 0.85rem; padding: 0.45rem 0.6rem; resize: vertical;"><?php echo htmlspecialchars($edit_prod['descricao'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group" style="margin-bottom: 0.75rem;">
                        <label style="font-size: 0.8rem; font-weight: 500;">Foto</label>
                        <input type="file" name="foto" accept="image/*" style="font-size: 0.85rem; padding: 0.35rem 0;" <?php echo $edit_prod ? '' : 'required'; ?> onchange="previewAdminImg(event)">
                        <?php if ($edit_prod && $edit_prod['imagem']): ?>
                            <div class="img-preview-wrap" style="margin-top: 0.5rem;">
                                <img src="<?php echo $edit_prod['imagem']; ?>" width="72" height="72" id="admin-img-preview">
                                <span style="font-size: 0.75rem; color: var(--text-light); display: block; margin-top: 0.2rem;">Atual (upload nova para substituir)</span>
                            </div>
                        <?php else: ?>
                            <div class="img-preview-wrap" style="margin-top: 0.5rem; display: none;" id="admin-img-preview-wrap">
                                <img width="72" height="72" id="admin-img-preview" style="object-fit: cover;">
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="flex gap-sm">
                        <button type="submit" name="salvar" class="btn btn-primary" style="flex: 1; font-size: 0.85rem; padding: 0.5rem;">
                            <?php echo $edit_prod ? '<i class="fas fa-save"></i> Salvar' : '<i class="fas fa-plus"></i> Criar'; ?>
                        </button>
                        <?php if ($edit_prod): ?>
                            <a href="admin.php" class="btn btn-secondary" style="font-size: 0.85rem; padding: 0.5rem 0.8rem;">Cancelar</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Lista de Produtos -->
        <div>
            <!-- Toolbar -->
            <div class="toolbar">
                <form method="get" class="input-group" style="flex: 1; gap: 0.5rem; flex-wrap: wrap;">
                    <input type="text" name="buscar" placeholder="Buscar produto..." value="<?php echo htmlspecialchars($buscar); ?>">
                    <button type="submit" class="btn btn-primary" style="font-size: 0.8rem; padding: 0.45rem 0.8rem;"><i class="fas fa-search"></i></button>
                    <?php if ($buscar): ?>
                        <a href="admin.php" class="btn btn-secondary" style="font-size: 0.8rem; padding: 0.45rem 0.8rem;">Limpar</a>
                    <?php endif; ?>
                </form>
                <select onchange="window.location='?ordenar='+this.value+'&direcao=<?php echo $direcao; ?>'">
                    <option value="id" <?php echo $ordenar === 'id' ? 'selected' : ''; ?>>Mais recentes</option>
                    <option value="nome" <?php echo $ordenar === 'nome' ? 'selected' : ''; ?>>Nome</option>
                    <option value="preco" <?php echo $ordenar === 'preco' ? 'selected' : ''; ?>>Preco</option>
                    <option value="estoque" <?php echo $ordenar === 'estoque' ? 'selected' : ''; ?>>Estoque</option>
                </select>
                <button class="btn-icon" onclick="var d='<?php echo $direcao; ?>';window.location='?ordenar=<?php echo $ordenar; ?>&direcao='+(d==='ASC'?'DESC':'ASC')" title="Inverter ordem">
                    <i class="fas fa-arrow-<?php echo $direcao === 'ASC' ? 'up' : 'down'; ?>"></i>
                </button>
            </div>

            <!-- Produtos -->
            <?php if ($produtos->num_rows == 0): ?>
                <div class="empty-admin">
                    <i class="fas fa-box-open"></i>
                    <p><?php echo $buscar ? 'Nenhum produto encontrado para "' . htmlspecialchars($buscar) . '".' : 'Nenhum produto cadastrado ainda.'; ?></p>
                    <?php if ($buscar): ?>
                        <a href="admin.php" class="btn btn-secondary" style="margin-top: 0.75rem;">Limpar busca</a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div style="font-size: 0.85rem; color: var(--text-light); margin-bottom: 0.75rem;">
                    <?php echo $produtos->num_rows; ?> produto(s) encontrado(s)
                </div>
                <?php while ($row = $produtos->fetch_assoc()): ?>
                <div class="prod-card">
                    <img src="<?php echo htmlspecialchars($row['imagem']); ?>" class="thumb" onerror="this.src='https://placehold.co/56x56/e0e0e0/999?text=?'">
                    <div class="info">
                        <span class="nome"><?php echo htmlspecialchars($row['nome']); ?></span>
                        <?php if (!empty($row['categoria'])): ?>
                            <span class="cat"><span class="category-tag"><?php echo htmlspecialchars($row['categoria']); ?></span></span>
                        <?php endif; ?>
                    </div>
                    <div class="preco">Kz <?php echo number_format($row['preco'], 2, ',', '.'); ?></div>
                    <div class="estoque-wrap">
                        <form method="post" style="display: flex; gap: 0.2rem; align-items: center;">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <input type="number" name="qtd" value="<?php echo $row['estoque']; ?>" min="0" title="Estoque">
                            <button type="submit" name="quick_estoque" class="btn-icon" title="Atualizar estoque"><i class="fas fa-check"></i></button>
                        </form>
                    </div>
                    <div class="acoes">
                        <a href="?edit=<?php echo $row['id']; ?>" class="btn-icon" title="Editar"><i class="fas fa-pen"></i></a>
                        <a href="?delete=<?php echo $row['id']; ?>" class="btn-icon danger" title="Excluir" onclick="return confirm('Excluir <?php echo htmlspecialchars($row['nome']); ?>?')"><i class="fas fa-trash"></i></a>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function previewAdminImg(e) {
    var file = e.target.files[0];
    if (!file) return;
    var reader = new FileReader();
    reader.onload = function(ev) {
        var preview = document.getElementById('admin-img-preview');
        var wrap = document.getElementById('admin-img-preview-wrap');
        if (preview) {
            preview.src = ev.target.result;
            preview.style.display = 'block';
        }
        if (wrap) wrap.style.display = 'block';
    };
    reader.readAsDataURL(file);
}
</script>

<?php echo $animais; ?>
<?php require_once 'footer.php'; ?>
