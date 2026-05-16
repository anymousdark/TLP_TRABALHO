<?php 
require_once 'header.php'; 

// Remover item
if (isset($_GET['remove'])) {
    $id = intval($_GET['remove']);
    unset($_SESSION['carrinho'][$id]);
    header("Location: carrinho.php");
    exit();
}

// Atualizar quantidade
if (isset($_POST['atualizar'])) {
    foreach ($_POST['qtd'] as $id => $qtd) {
        $id = intval($id);
        $qtd = intval($qtd);
        if ($qtd <= 0) {
            unset($_SESSION['carrinho'][$id]);
        } else {
            // Verificar estoque máximo
            $res = $conn->query("SELECT estoque FROM produtos WHERE id = $id");
            $prod = $res->fetch_assoc();
            if ($qtd > $prod['estoque']) {
                $_SESSION['carrinho'][$id] = $prod['estoque'];
            } else {
                $_SESSION['carrinho'][$id] = $qtd;
            }
        }
    }
    header("Location: carrinho.php");
    exit();
}

$total = 0;
$itens = [];

if (isset($_SESSION['carrinho']) && !empty($_SESSION['carrinho'])) {
    $ids = implode(',', array_keys($_SESSION['carrinho']));
    $result = $conn->query("SELECT * FROM produtos WHERE id IN ($ids)");
    while ($row = $result->fetch_assoc()) {
        $row['quantidade'] = $_SESSION['carrinho'][$row['id']];
        $row['subtotal'] = $row['preco'] * $row['quantidade'];
        $total += $row['subtotal'];
        $itens[] = $row;
    }
}
?>

<div class="container">
    <h1>Seu Carrinho</h1>

    <?php if (empty($itens)): ?>
        <p>Seu carrinho está vazio. <a href="index.php">Voltar às compras.</a></p>
    <?php else: ?>
        <form method="post">
            <table>
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Preço</th>
                        <th>Quantidade</th>
                        <th>Subtotal</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($itens as $item): ?>
                    <tr>
                        <td>
                            <strong><?php echo $item['nome']; ?></strong>
                        </td>
                        <td>R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?></td>
                        <td>
                            <input type="number" name="qtd[<?php echo $item['id']; ?>]" value="<?php echo $item['quantidade']; ?>" min="1" max="<?php echo $item['estoque']; ?>" style="width: 70px;">
                        </td>
                        <td>R$ <?php echo number_format($item['subtotal'], 2, ',', '.'); ?></td>
                        <td>
                            <a href="?remove=<?php echo $item['id']; ?>" class="btn btn-danger" style="padding: 0.25rem 0.5rem;">Remover</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="total-section">
                Total: R$ <?php echo number_format($total, 2, ',', '.'); ?>
            </div>

            <div style="display: flex; justify-content: space-between; margin-top: 2rem;">
                <button type="submit" name="atualizar" class="btn btn-primary" style="background: var(--secondary);">Atualizar Carrinho</button>
                <a href="finalizar.php" class="btn btn-success">Finalizar Compra</a>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>
