<?php 
require_once 'header.php'; 

$animais = '<div class="animal-corner animal-corner-bl">' . animal_leao(70) . '</div>';
$animais .= '<div class="animal-corner animal-corner-br">' . animal_zebra(60) . '</div>';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$stmt = $conn->prepare("SELECT * FROM pedidos WHERE usuario_id = ? ORDER BY data_pedido DESC");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

$pg_metodos = [];
$res_mp = $conn->query("SELECT * FROM metodos_pagamento ORDER BY id ASC");
while ($mp = $res_mp->fetch_assoc()) {
    $pg_metodos[$mp['id']] = $mp['nome'];
}
?>

<style>
.order-card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    margin-bottom: 1.25rem;
    box-shadow: var(--shadow);
    transition: transform 0.15s;
}
.order-card:hover { transform: translateY(-1px); }
.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid var(--border);
}
.order-id { font-size: 1.1rem; font-weight: 700; }
.order-date { font-size: 0.85rem; color: var(--text-light); }
.order-body {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1rem;
}
.order-info-group { font-size: 0.9rem; }
.order-info-group h4 {
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--text-light);
    margin-bottom: 0.4rem;
}
.order-info-group .value { font-weight: 600; display: block; }
.order-info-group .sub { font-size: 0.8rem; color: var(--text-light); }
.order-items {
    margin-top: 0.5rem;
    padding-top: 0.75rem;
    border-top: 1px solid var(--border);
}
.order-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.3rem 0;
    font-size: 0.85rem;
}
.order-item .qty { color: var(--text-light); margin-right: 0.5rem; }
.order-item .name { flex: 1; }
.order-item .price { font-weight: 600; white-space: nowrap; }
.order-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-top: 0.75rem;
    padding-top: 0.75rem;
    border-top: 1px solid var(--border);
}
.order-total { font-size: 1.1rem; font-weight: 700; }
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
}
.empty-state i { font-size: 3rem; color: var(--text-light); margin-bottom: 1rem; display: block; }
</style>

<div class="container" style="max-width: 720px;">
    <h1>Meus Pedidos</h1>

    <?php if ($result->num_rows == 0): ?>
        <div class="empty-state">
            <i class="fas fa-receipt"></i>
            <p>Voce ainda nao realizou nenhum pedido.</p>
            <a href="index.php" class="btn btn-primary" style="margin-top: 1rem;">Ir as Compras</a>
        </div>
    <?php else: ?>
        <?php while ($row = $result->fetch_assoc()): 
            $metodo = $pg_metodos[$row['metodo_pagamento']] ?? $row['metodo_pagamento'];
            // Fallback para pedidos antigos com keys tipo 'multicaixa'
            if (!isset($pg_metodos[$row['metodo_pagamento']]) && !is_numeric($row['metodo_pagamento'])) {
                foreach ($pg_metodos as $nome) {
                    if (stripos($nome, $row['metodo_pagamento']) !== false) { $metodo = $nome; break; }
                }
            }
            $pedido_id = $row['id'];
            $stmt_itens = $conn->prepare("SELECT i.*, p.nome, p.imagem FROM itens_pedido i JOIN produtos p ON i.produto_id = p.id WHERE i.pedido_id = ?");
            $stmt_itens->bind_param("i", $pedido_id);
            $stmt_itens->execute();
            $itens = $stmt_itens->get_result();
        ?>
        <div class="order-card">
            <div class="order-header">
                <div>
                    <span class="order-id">#<?php echo $pedido_id; ?></span>
                    <span class="order-date"> &middot; <?php echo date('d/m/Y H:i', strtotime($row['data_pedido'])); ?></span>
                </div>
                <span class="badge <?php 
                    echo $row['status'] === 'pago' ? 'badge-success' : ($row['status'] === 'cancelado' ? 'badge-out' : 'badge-pendente'); 
                ?>"><?php echo ucfirst($row['status']); ?></span>
            </div>

            <div class="order-body">
                <div class="order-info-group">
                    <h4>Pagamento</h4>
                    <span class="value"><?php echo htmlspecialchars($metodo); ?></span>
                </div>
                <div class="order-info-group">
                    <h4>Entrega</h4>
                    <span class="value"><?php echo htmlspecialchars($row['endereco']); ?></span>
                    <span class="sub">Tel: <?php echo htmlspecialchars($row['telefone']); ?></span>
                </div>
            </div>

            <div class="order-items">
                <?php while ($item = $itens->fetch_assoc()): ?>
                <div class="order-item">
                    <span class="qty"><?php echo $item['quantidade']; ?>x</span>
                    <?php if ($item['imagem']): ?>
                        <img src="<?php echo htmlspecialchars($item['imagem']); ?>" width="24" height="24" style="border-radius: 4px; object-fit: cover; margin-right: 0.5rem;">
                    <?php endif; ?>
                    <span class="name"><?php echo htmlspecialchars($item['nome']); ?></span>
                    <span class="price">Kz <?php echo number_format($item['preco_unitario'] * $item['quantidade'], 2, ',', '.'); ?></span>
                </div>
                <?php endwhile; ?>
            </div>

            <div class="order-footer">
                <span></span>
                <span class="order-total">Total: Kz <?php echo number_format($row['total'], 2, ',', '.'); ?></span>
            </div>
        </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>

<?php echo $animais; ?>
<?php require_once 'footer.php'; ?>
