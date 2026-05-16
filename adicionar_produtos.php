<?php
require_once 'config.php';

$produtos = [
    [
        'nome' => 'PC Gamer Elite Edition',
        'descricao' => 'RTX 4060 + Ryzen 7 + 16GB RAM + 1TB SSD. Montado e testado.',
        'preco' => 8500.00,
        'estoque' => 3,
        'imagem' => 'https://images.unsplash.com/photo-1587829191301-441f6e9e7e3d?w=400&h=300&fit=crop'
    ],
    [
        'nome' => 'Corsair CV650 80 Plus',
        'descricao' => 'Fonte 650W com certificação Bronze. Energia limpa.',
        'preco' => 480.00,
        'estoque' => 12,
        'imagem' => 'https://images.unsplash.com/photo-1625948515291-69613efd103f?w=400&h=300&fit=crop'
    ],
    [
        'nome' => 'HyperX Cloud II 7.1',
        'descricao' => 'Som surround e conforto lendário para longas sessões.',
        'preco' => 750.00,
        'estoque' => 8,
        'imagem' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=300&fit=crop'
    ],
    [
        'nome' => 'Razer BlackWidow V3',
        'descricao' => 'Teclado Mecânico RGB com switches verdes táteis.',
        'preco' => 950.00,
        'estoque' => 15,
        'imagem' => 'https://images.unsplash.com/photo-1587829191301-441f6e9e7e3d?w=400&h=300&fit=crop'
    ],
    [
        'nome' => 'Logitech G502 HERO',
        'descricao' => 'Sensor 25K, 11 botões programáveis. O mouse mais vendido do mundo.',
        'preco' => 350.00,
        'estoque' => 25,
        'imagem' => 'https://images.unsplash.com/photo-1527814050087-3793815479db?w=400&h=300&fit=crop'
    ],
    [
        'nome' => 'Monitor LG UltraGear 27"',
        'descricao' => '144Hz, 1ms, IPS Full HD. O monitor dos pro-players.',
        'preco' => 1800.00,
        'estoque' => 6,
        'imagem' => 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=400&h=300&fit=crop'
    ],
    [
        'nome' => 'Samsung 980 1TB NVMe',
        'descricao' => 'Leitura até 3500MB/s. O padrão ouro em SSDs.',
        'preco' => 650.00,
        'estoque' => 18,
        'imagem' => 'https://images.unsplash.com/photo-1597872200969-2b65d56bd16b?w=400&h=300&fit=crop'
    ],
    [
        'nome' => 'Corsair Vengeance 16GB',
        'descricao' => 'Dual Channel 3200MHz DDR4. Estabilidade absoluta.',
        'preco' => 550.00,
        'estoque' => 22,
        'imagem' => 'https://images.unsplash.com/photo-1597872200969-2b65d56bd16b?w=400&h=300&fit=crop'
    ],
    [
        'nome' => 'AMD Ryzen 7 5800X',
        'descricao' => '8 núcleos de alta performance para produtividade e jogos.',
        'preco' => 1900.00,
        'estoque' => 7,
        'imagem' => 'https://images.unsplash.com/photo-1591290619520-a9f37a8c5e19?w=400&h=300&fit=crop'
    ],
    [
        'nome' => 'AMD Ryzen 5 5600',
        'descricao' => '6 núcleos, 12 threads. Performance sólida para gaming.',
        'preco' => 1200.00,
        'estoque' => 10,
        'imagem' => 'https://images.unsplash.com/photo-1591290619520-a9f37a8c5e19?w=400&h=300&fit=crop'
    ],
    [
        'nome' => 'NVIDIA RTX 4070 Super',
        'descricao' => '12GB GDDR6X, Perfeita para 1440p Ultra.',
        'preco' => 4500.00,
        'estoque' => 4,
        'imagem' => 'https://images.unsplash.com/photo-1587829191301-441f6e9e7e3d?w=400&h=300&fit=crop'
    ],
    [
        'nome' => 'NVIDIA RTX 4060 Ti',
        'descricao' => '8GB GDDR6, Ray Tracing, DLSS 3.0. A rainha do custo-benefício.',
        'preco' => 2800.00,
        'estoque' => 9,
        'imagem' => 'https://images.unsplash.com/photo-1587829191301-441f6e9e7e3d?w=400&h=300&fit=crop'
    ]
];

$adicionados = 0;
$erros = [];

foreach ($produtos as $p) {
    $stmt = $conn->prepare("INSERT INTO produtos (nome, descricao, preco, estoque, imagem) VALUES (?, ?, ?, ?, ?)");
    if ($stmt !== false) {
        $stmt->bind_param("ssdis", $p['nome'], $p['descricao'], $p['preco'], $p['estoque'], $p['imagem']);
        if ($stmt->execute()) {
            $adicionados++;
        } else {
            $erros[] = $p['nome'] . ": " . $stmt->error;
        }
    } else {
        $erros[] = "Erro ao preparar: " . $conn->error;
    }
}

echo "<div style='text-align: center; padding: 2rem; font-size: 1.2rem; background: #f0f9ff; border-radius: 1rem; margin: 2rem;'>";
echo "<h1>✅ " . $adicionados . " produtos adicionados com sucesso!</h1>";
if (!empty($erros)) {
    echo "<div style='color: red; margin-top: 1rem;'>";
    foreach ($erros as $e) {
        echo "<p>❌ $e</p>";
    }
    echo "</div>";
}
echo "<p style='margin-top: 1.5rem;'><a href='index.php' style='color: #2563eb; text-decoration: none; font-weight: bold; font-size: 1.1rem;'>👉 Ver Produtos na Loja</a></p>";
echo "<p style='color: #666; margin-top: 1rem;'>Agora pode deletar este arquivo: <strong>adicionar_produtos.php</strong></p>";
echo "</div>";
?>
