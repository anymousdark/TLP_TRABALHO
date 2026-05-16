<?php 
require_once 'header.php'; 

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$stmt = $conn->prepare("SELECT * FROM pedidos WHERE usuario_id = ? ORDER BY data_pedido DESC");
if ($stmt === false) {
    die("Erro ao preparar SELECT: " . $conn->error);
}
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container">
    <h1>Meus Pedidos</h1>

    <?php if ($result->num_rows == 0): ?>
        <p>Você ainda não realizou nenhum pedido.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID Pedido</th>
                    <th>Data</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Itens</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>#<?php echo $row['id']; ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($row['data_pedido'])); ?></td>
                    <td>R$ <?php echo number_format($row['total'], 2, ',', '.'); ?></td>
                    <td><span class="badge badge-success"><?php echo ucfirst($row['status']); ?></span></td>
                    <td>
                        <div style="font-size: 0.8rem; color: var(--text-light);">
                            <?php 
                            $pedido_id = $row['id'];
                            $stmt_itens = $conn->prepare("SELECT i.*, p.nome FROM itens_pedido i JOIN produtos p ON i.produto_id = p.id WHERE i.pedido_id = ?");
                            if ($stmt_itens !== false) {
                                $stmt_itens->bind_param("i", $pedido_id);
                                $stmt_itens->execute();
                                $itens_res = $stmt_itens->get_result();
                                while ($item = $itens_res->fetch_assoc()) {
                                    echo $item['quantidade'] . "x " . $item['nome'] . "<br>";
                                }
                            }
                            ?>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>
