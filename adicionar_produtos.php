<?php
require_once 'config.php';

$produtos = [
    ['nome' => 'PC Gamer Elite Edition', 'descricao' => 'RTX 4060 + Ryzen 7 + 16GB RAM + 1TB SSD. Montado e testado.', 'preco' => 8500.00, 'estoque' => 3, 'imagem' => 'uploads/pc-gamer.jpg'],
    ['nome' => 'Corsair CV650 80 Plus', 'descricao' => 'Fonte 650W com certificacao Bronze.', 'preco' => 480.00, 'estoque' => 12, 'imagem' => 'uploads/fonte.jpg'],
    ['nome' => 'HyperX Cloud II 7.1', 'descricao' => 'Som surround e conforto lendario para longas sessoes.', 'preco' => 750.00, 'estoque' => 8, 'imagem' => 'uploads/fone.jpg'],
    ['nome' => 'Razer BlackWidow V3', 'descricao' => 'Teclado Mecanico RGB com switches verdes tateis.', 'preco' => 950.00, 'estoque' => 15, 'imagem' => 'uploads/teclado.jpg'],
    ['nome' => 'Logitech G502 HERO', 'descricao' => 'Sensor 25K, 11 botoes programaveis.', 'preco' => 350.00, 'estoque' => 25, 'imagem' => 'uploads/mouse.jpg'],
    ['nome' => 'Monitor LG UltraGear 27"', 'descricao' => '144Hz, 1ms, IPS Full HD.', 'preco' => 1800.00, 'estoque' => 6, 'imagem' => 'uploads/monitor.jpg'],
    ['nome' => 'Samsung 980 1TB NVMe', 'descricao' => 'Leitura ate 3500MB/s.', 'preco' => 650.00, 'estoque' => 18, 'imagem' => 'uploads/ssd.jpg'],
    ['nome' => 'Corsair Vengeance 16GB', 'descricao' => 'Dual Channel 3200MHz DDR4.', 'preco' => 550.00, 'estoque' => 22, 'imagem' => 'uploads/ssd.jpg'],
    ['nome' => 'AMD Ryzen 7 5800X', 'descricao' => '8 nucleos de alta performance.', 'preco' => 1900.00, 'estoque' => 7, 'imagem' => 'uploads/processador.jpg'],
    ['nome' => 'NVIDIA RTX 4070 Super', 'descricao' => '12GB GDDR6X, perfeita para 1440p Ultra.', 'preco' => 4500.00, 'estoque' => 4, 'imagem' => 'uploads/pc-gamer.jpg']
];

$adicionados = 0;
$erros = [];
foreach ($produtos as $p) {
    $stmt = $conn->prepare("INSERT INTO produtos (nome, descricao, preco, estoque, imagem) VALUES (?, ?, ?, ?, ?)");
    if ($stmt !== false) {
        $stmt->bind_param("ssdis", $p['nome'], $p['descricao'], $p['preco'], $p['estoque'], $p['imagem']);
        if ($stmt->execute()) $adicionados++;
        else $erros[] = $p['nome'] . ": " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-ao">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Produtos - Lakastech</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-body">
        <div class="login-container" style="text-align: center;">
            <h1><?php echo $adicionados; ?> produtos adicionados!</h1>
            <?php if (!empty($erros)): ?>
                <div class="alert alert-danger"><?php echo implode('<br>', $erros); ?></div>
            <?php endif; ?>
            <a href="index.php" class="btn btn-primary">Ver na Loja</a>
            <p class="text-muted" style="margin-top: 1rem;">Deletar este arquivo apos uso</p>
        </div>
    </div>
</body>
</html>
