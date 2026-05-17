<?php
require_once 'config.php';

$produtos = [
    ['nome' => 'Smartphone Pro X', 'descricao' => 'Smartphone de ultima geracao com camera 108MP e bateria de 5000mAh', 'preco' => 1999.99, 'estoque' => 15, 'imagem' => 'uploads/smartphone.jpg'],
    ['nome' => 'Laptop Ultra', 'descricao' => 'Laptop leve e potente com processador Intel i7 e 16GB RAM', 'preco' => 4999.99, 'estoque' => 8, 'imagem' => 'uploads/notebook.jpg'],
    ['nome' => 'Fone Bluetooth Premium', 'descricao' => 'Fone sem fio com cancelamento de ruido ativo e 30h de bateria', 'preco' => 399.99, 'estoque' => 32, 'imagem' => 'uploads/fone.jpg'],
    ['nome' => 'Tablet 10.5"', 'descricao' => 'Tablet com tela OLED, perfeito para trabalho e entretenimento', 'preco' => 2499.99, 'estoque' => 12, 'imagem' => 'uploads/tablet.jpg'],
    ['nome' => 'Camera Mirrorless', 'descricao' => 'Camera profissional com sensor full frame e 8K de resolucao', 'preco' => 7999.99, 'estoque' => 5, 'imagem' => 'uploads/camera.jpg'],
    ['nome' => 'Smart Watch Elite', 'descricao' => 'Relogio inteligente com GPS, monitor cardiaco e 7 dias de bateria', 'preco' => 1299.99, 'estoque' => 20, 'imagem' => 'uploads/smartwatch.jpg'],
    ['nome' => 'Monitor 4K', 'descricao' => 'Monitor 27" 4K com HDR e taxa de atualizacao 144Hz', 'preco' => 1899.99, 'estoque' => 10, 'imagem' => 'uploads/monitor.jpg'],
    ['nome' => 'Teclado Mecanico', 'descricao' => 'Teclado mecanico RGB com switches personalizaveis', 'preco' => 399.99, 'estoque' => 25, 'imagem' => 'uploads/teclado.jpg'],
    ['nome' => 'Mouse Gamer', 'descricao' => 'Mouse com sensor 16000 DPI e 8 botoes programaveis', 'preco' => 199.99, 'estoque' => 40, 'imagem' => 'uploads/mouse.jpg'],
    ['nome' => 'Carregador Rapido', 'descricao' => 'Carregador USB-C 65W com carga rapida para multiplos dispositivos', 'preco' => 149.99, 'estoque' => 50, 'imagem' => 'uploads/fonte.jpg']
];

$adicionados = 0;
foreach ($produtos as $p) {
    $stmt = $conn->prepare("INSERT INTO produtos (nome, descricao, preco, estoque, imagem) VALUES (?, ?, ?, ?, ?)");
    if ($stmt !== false) {
        $stmt->bind_param("ssdis", $p['nome'], $p['descricao'], $p['preco'], $p['estoque'], $p['imagem']);
        if ($stmt->execute()) $adicionados++;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-ao">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos - Lakastech</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-body">
        <div class="login-container" style="text-align: center;">
            <h1><?php echo $adicionados; ?> produtos adicionados!</h1>
            <a href="index.php" class="btn btn-primary">Ver na Loja</a>
            <p class="text-muted" style="margin-top: 1rem;">Deletar este arquivo apos uso</p>
        </div>
    </div>
</body>
</html>
