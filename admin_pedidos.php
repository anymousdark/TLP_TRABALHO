<?php 
require_once 'header.php'; 

$animais = '<div class="animal-corner animal-corner-bl">' . animal_elefante(70) . '</div>';
$animais .= '<div class="animal-corner animal-corner-br">' . animal_girafa(70) . '</div>';

if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Alterar status
if (isset($_POST['alterar_status'])) {
    $pedido_id = intval($_POST['pedido_id']);
    $novo_status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE pedidos SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $novo_status, $pedido_id);
    $stmt->execute();
    header("Location: admin_pedidos.php");
    exit();
}

// Carregar metodos de pagamento
$todos_metodos = [];
$res_mp = $conn->query("SELECT * FROM metodos_pagamento ORDER BY id ASC");
while ($mp = $res_mp->fetch_assoc()) {
    $todos_metodos[$mp['id']] = $mp['nome'];
}

// Filtros
$filtro_status = $_GET['status'] ?? '';
$busca = trim($_GET['busca'] ?? '');

$sql = "SELECT p.*, u.nome AS cliente, u.email FROM pedidos p JOIN usuarios u ON p.usuario_id = u.id";
$condicoes = [];
$params = [];
$tipos = "";

if ($filtro_status) {
    $condicoes[] = "p.status = ?";
    $params[] = $filtro_status;
    $tipos .= "s";
}
if ($busca) {
    $condicoes[] = "(p.id = ? OR u.nome LIKE ? OR u.email LIKE ?)";
    $params[] = is_numeric($busca) ? intval($busca) : 0;
    $busca_like = "%$busca%";
    $params[] = $busca_like;
    $params[] = $busca_like;
    $tipos .= "iss";
}

if ($condicoes) {
    $sql .= " WHERE " . implode(" AND ", $condicoes);
}
$sql .= " ORDER BY p.data_pedido DESC";

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($tipos, ...$params);
}
$stmt->execute();
$pedidos = $stmt->get_result();

// Stats
$stats = [];
$stats['todos'] = intval($conn->query("SELECT COUNT(*) as c FROM pedidos")->fetch_assoc()['c']);
$stats['pendente'] = intval($conn->query("SELECT COUNT(*) as c FROM pedidos WHERE status='pendente'")->fetch_assoc()['c']);
$stats['pago'] = intval($conn->query("SELECT COUNT(*) as c FROM pedidos WHERE status='pago'")->fetch_assoc()['c']);
$stats['cancelado'] = intval($conn->query("SELECT COUNT(*) as c FROM pedidos WHERE status='cancelado'")->fetch_assoc()['c']);
?>

<style>
.admin-pedidos-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1.5rem;
}
.filtros-bar {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
    align-items: center;
    margin-bottom: 1.5rem;
}
.filtros-bar form {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    align-items: center;
    flex: 1;
}
.filtros-bar input, .filtros-bar select {
    padding: 0.45rem 0.7rem;
    font-size: 0.85rem;
    border-radius: var(--radius-sm);
    border: 1px solid var(--border);
    background: var(--card);
    color: var(--text);
}
.filtros-bar input { flex: 1; min-width: 160px; }
.stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 0.75rem;
    margin-bottom: 1.5rem;
}
.stat-pill {
    padding: 0.6rem 1rem;
    border-radius: var(--radius-lg);
    text-align: center;
    border: 1px solid var(--border);
    background: var(--card);
    cursor: pointer;
    transition: all 0.15s;
    text-decoration: none;
    color: var(--text);
}
.stat-pill:hover { border-color: var(--primary); transform: translateY(-1px); }
.stat-pill.active { border-color: var(--primary); background: rgba(0, 113, 227, 0.06); }
.stat-pill .num { font-size: 1.35rem; font-weight: 700; display: block; line-height: 1.2; }
.stat-pill .label { font-size: 0.75rem; color: var(--text-light); }
.stat-pill.pendente .num { color: #c17e00; }
.stat-pill.pago .num { color: #1a7a3a; }
.stat-pill.cancelado .num { color: #b3241c; }

.pedido-card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    margin-bottom: 1rem;
    box-shadow: var(--shadow);
    overflow: hidden;
}
.pedido-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.75rem;
    padding: 1rem 1.5rem;
    background: var(--background);
    border-bottom: 1px solid var(--border);
}
.pedido-card-header .id-data { display: flex; align-items: center; gap: 1rem; flex-wrap: wrap; }
.pedido-card-header .id-data .id { font-size: 1.1rem; font-weight: 700; }
.pedido-card-header .id-data .data { font-size: 0.85rem; color: var(--text-light); }
.pedido-card-header .cliente-info { font-size: 0.85rem; }
.pedido-card-header .cliente-info .nome { font-weight: 600; }
.pedido-card-header .cliente-info .email { color: var(--text-light); font-size: 0.8rem; }

.pedido-card-body {
    padding: 1.25rem 1.5rem;
}
.pedido-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.25rem;
}
@media (max-width: 700px) { .pedido-grid { grid-template-columns: 1fr; } }
.pedido-info h4 {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--text-light);
    margin-bottom: 0.4rem;
}
.pedido-info .valor { font-weight: 600; font-size: 0.95rem; display: block; }
.pedido-info .sub { font-size: 0.8rem; color: var(--text-light); }

.pedido-itens { border-top: 1px solid var(--border); padding-top: 0.75rem; }
.pedido-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.4rem 0;
    font-size: 0.9rem;
}
.pedido-item img {
    width: 32px; height: 32px;
    border-radius: 4px;
    object-fit: cover;
    flex-shrink: 0;
    background: var(--background);
}
.pedido-item .qtd { color: var(--text-light); min-width: 2rem; }
.pedido-item .nome-prod { flex: 1; }
.pedido-item .preco-item { font-weight: 600; white-space: nowrap; }

.pedido-card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    padding: 1rem 1.5rem;
    border-top: 1px solid var(--border);
    background: var(--background);
}
.pedido-total { font-size: 1.15rem; font-weight: 700; }
.status-form {
    display: flex;
    gap: 0.35rem;
    align-items: center;
}
.status-form select {
    padding: 0.35rem 0.6rem;
    font-size: 0.85rem;
    border-radius: var(--radius-sm);
    border: 1px solid var(--border);
    background: var(--card);
    color: var(--text);
}
.empty-admin {
    text-align: center;
    padding: 3rem 1rem;
    color: var(--text-light);
}
.empty-admin i { font-size: 2.5rem; display: block; margin-bottom: 0.75rem; }
</style>

<div class="container" style="max-width: 960px;">

    <!-- Cabecalho -->
    <div class="admin-pedidos-header">
        <div>
            <h1 style="margin-bottom: 0.25rem;">Pedidos</h1>
            <p style="color: var(--text-light); font-size: 0.9rem;">Gestao de pedidos da loja</p>
        </div>
        <div class="flex gap-sm">
            <a href="admin.php" class="btn btn-secondary">Produtos</a>
            <a href="admin_usuarios.php" class="btn btn-secondary">Usuarios</a>
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-row">
        <a href="admin_pedidos.php" class="stat-pill <?php echo !$filtro_status ? 'active' : ''; ?>">
            <span class="num"><?php echo $stats['todos']; ?></span>
            <span class="label">Todos</span>
        </a>
        <a href="?status=pendente" class="stat-pill pendente <?php echo $filtro_status === 'pendente' ? 'active' : ''; ?>">
            <span class="num"><?php echo $stats['pendente']; ?></span>
            <span class="label">Pendentes</span>
        </a>
        <a href="?status=pago" class="stat-pill pago <?php echo $filtro_status === 'pago' ? 'active' : ''; ?>">
            <span class="num"><?php echo $stats['pago']; ?></span>
            <span class="label">Pagos</span>
        </a>
        <a href="?status=cancelado" class="stat-pill cancelado <?php echo $filtro_status === 'cancelado' ? 'active' : ''; ?>">
            <span class="num"><?php echo $stats['cancelado']; ?></span>
            <span class="label">Cancelados</span>
        </a>
    </div>

    <!-- Filtros -->
    <div class="filtros-bar">
        <form method="get">
            <?php if ($filtro_status): ?>
                <input type="hidden" name="status" value="<?php echo $filtro_status; ?>">
            <?php endif; ?>
            <input type="text" name="busca" placeholder="Buscar por ID, nome ou email..." value="<?php echo htmlspecialchars($busca); ?>">
            <button type="submit" class="btn btn-primary" style="font-size: 0.8rem; padding: 0.45rem 0.8rem;"><i class="fas fa-search"></i></button>
            <?php if ($busca || $filtro_status): ?>
                <a href="admin_pedidos.php" class="btn btn-secondary" style="font-size: 0.8rem; padding: 0.45rem 0.8rem;">Limpar</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Lista -->
    <?php if ($pedidos->num_rows == 0): ?>
        <div class="empty-admin">
            <i class="fas fa-receipt"></i>
            <p><?php echo $busca ? 'Nenhum pedido encontrado para "' . htmlspecialchars($busca) . '".' : 'Nenhum pedido encontrado.'; ?></p>
        </div>
    <?php else: ?>
        <div style="font-size: 0.85rem; color: var(--text-light); margin-bottom: 0.75rem;">
            <?php echo $pedidos->num_rows; ?> pedido(s) encontrado(s)
        </div>

        <?php while ($pedido = $pedidos->fetch_assoc()): 
            $pedido_id = $pedido['id'];
            $metodo = $todos_metodos[$pedido['metodo_pagamento']] ?? $pedido['metodo_pagamento'];
            if (!isset($todos_metodos[$pedido['metodo_pagamento']]) && !is_numeric($pedido['metodo_pagamento'])) {
                foreach ($todos_metodos as $nome) {
                    if (stripos($nome, $pedido['metodo_pagamento']) !== false) { $metodo = $nome; break; }
                }
            }
            $stmt_itens = $conn->prepare("SELECT i.*, p.nome, p.imagem FROM itens_pedido i JOIN produtos p ON i.produto_id = p.id WHERE i.pedido_id = ?");
            $stmt_itens->bind_param("i", $pedido_id);
            $stmt_itens->execute();
            $itens = $stmt_itens->get_result();
        ?>
        <div class="pedido-card">
            <!-- Header -->
            <div class="pedido-card-header">
                <div class="id-data">
                    <span class="id">#<?php echo $pedido_id; ?></span>
                    <span class="data"><?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?></span>
                    <span class="badge <?php 
                        echo $pedido['status'] === 'pago' ? 'badge-success' : ($pedido['status'] === 'cancelado' ? 'badge-out' : 'badge-pendente'); 
                    ?>"><?php echo ucfirst($pedido['status']); ?></span>
                </div>
                <div class="cliente-info">
                    <span class="nome"><?php echo htmlspecialchars($pedido['cliente']); ?></span>
                    <span class="email"> &middot; <?php echo htmlspecialchars($pedido['email']); ?></span>
                </div>
            </div>

            <!-- Body -->
            <div class="pedido-card-body">
                <div class="pedido-grid">
                    <div class="pedido-info">
                        <h4>Pagamento</h4>
                        <span class="valor"><?php echo htmlspecialchars($metodo); ?></span>
                    </div>
                    <div class="pedido-info">
                        <h4>Entrega</h4>
                        <span class="valor"><?php echo htmlspecialchars($pedido['endereco']); ?></span>
                        <span class="sub">Tel: <?php echo htmlspecialchars($pedido['telefone']); ?></span>
                    </div>
                </div>

                <div class="pedido-itens">
                    <?php while ($item = $itens->fetch_assoc()): ?>
                    <div class="pedido-item">
                        <?php if ($item['imagem']): ?>
                            <img src="<?php echo htmlspecialchars($item['imagem']); ?>">
                        <?php endif; ?>
                        <span class="qtd"><?php echo $item['quantidade']; ?>x</span>
                        <span class="nome-prod"><?php echo htmlspecialchars($item['nome']); ?></span>
                        <span class="preco-item">Kz <?php echo number_format($item['preco_unitario'] * $item['quantidade'], 2, ',', '.'); ?></span>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- Footer -->
            <div class="pedido-card-footer">
                <span class="pedido-total">Total: Kz <?php echo number_format($pedido['total'], 2, ',', '.'); ?></span>
                <form method="post" class="status-form">
                    <input type="hidden" name="pedido_id" value="<?php echo $pedido_id; ?>">
                    <select name="status">
                        <option value="pendente" <?php echo $pedido['status'] === 'pendente' ? 'selected' : ''; ?>>Pendente</option>
                        <option value="pago" <?php echo $pedido['status'] === 'pago' ? 'selected' : ''; ?>>Pago</option>
                        <option value="cancelado" <?php echo $pedido['status'] === 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                    </select>
                    <button type="submit" name="alterar_status" class="btn btn-primary" style="padding: 0.35rem 0.7rem; font-size: 0.8rem;">Actualizar</button>
                </form>
            </div>
        </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>

<?php echo $animais; ?>
<?php require_once 'footer.php'; ?>
