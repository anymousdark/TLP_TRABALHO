<?php 
require_once 'header.php'; 

// Adicionar ao carrinho
if (isset($_GET['add'])) {
    $id = intval($_GET['add']);
    
    // Verificar estoque
    $sql = "SELECT estoque FROM produtos WHERE id = $id";
    $res = $conn->query($sql);
    $prod = $res->fetch_assoc();
    
    if ($prod && $prod['estoque'] > 0) {
        if (!isset($_SESSION['carrinho'])) {
            $_SESSION['carrinho'] = [];
        }
        
        if (isset($_SESSION['carrinho'][$id])) {
            $_SESSION['carrinho'][$id]++;
        } else {
            $_SESSION['carrinho'][$id] = 1;
        }
        header("Location: carrinho.php");
        exit();
    } else {
        echo "<script>alert('Produto esgotado!');</script>";
    }
}

$result = $conn->query("SELECT * FROM produtos ORDER BY id DESC");
?>

<h1>Nossos Produtos</h1>

<div class="products-grid">
    <?php while($row = $result->fetch_assoc()): ?>
    <div class="product-card">
        <img src="<?php echo $row['imagem'] ?: 'https://via.placeholder.com/400x300'; ?>" alt="<?php echo $row['nome']; ?>" class="product-image">
        <div class="product-info">
            <h3 class="product-name"><?php echo $row['nome']; ?></h3>
            <p class="product-desc"><?php echo $row['descricao']; ?></p>
            <div class="product-price">R$ <?php echo number_format($row['preco'], 2, ',', '.'); ?></div>
            
            <div style="margin-bottom: 1rem;">
                <?php if (isset($row['estoque']) && $row['estoque'] > 0): ?>
                    <span class="badge badge-stock">Em estoque: <?php echo $row['estoque']; ?></span>
                <?php else: ?>
                    <span class="badge badge-out">Esgotado</span>
                <?php endif; ?>
            </div>

            <?php if (isset($row['estoque']) && $row['estoque'] > 0): ?>
                <a href="?add=<?php echo $row['id']; ?>" class="btn btn-primary" style="width: 100%; text-align: center;">Adicionar ao Carrinho</a>
            <?php else: ?>
                <button class="btn" style="width: 100%; cursor: not-allowed; background: #ccc;" disabled>Indisponível</button>
            <?php endif; ?>
        </div>
    </div>
    <?php endwhile; ?>
</div>

<?php require_once 'footer.php'; ?>
