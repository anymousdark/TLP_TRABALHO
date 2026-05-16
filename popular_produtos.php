<?php
require_once 'config.php';

// Limpar produtos anteriores (opcional)
// $conn->query("DELETE FROM produtos");

$produtos = [
    [
        'nome' => 'Smartphone Pro X',
        'descricao' => 'Smartphone de última geração com câmera 108MP e bateria de 5000mAh',
        'preco' => 1999.99,
        'estoque' => 15,
        'imagem' => 'https://images.unsplash.com/photo-1511707267537-b85faf00021e?w=400&h=300&fit=crop'
    ],
    [
        'nome' => 'Laptop Ultra',
        'descricao' => 'Laptop leve e potente com processador Intel i7 e 16GB RAM',
        'preco' => 4999.99,
        'estoque' => 8,
        'imagem' => 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=400&h=300&fit=crop'
    ],
    [
        'nome' => 'Fone Bluetooth Premium',
        'descricao' => 'Fone sem fio com cancelamento de ruído ativo e 30h de bateria',
        'preco' => 399.99,
        'estoque' => 32,
        'imagem' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=300&fit=crop'
    ],
    [
        'nome' => 'Tablet 10.5"',
        'descricao' => 'Tablet com tela OLED, perfeito para trabalho e entretenimento',
        'preco' => 2499.99,
        'estoque' => 12,
        'imagem' => 'https://images.unsplash.com/photo-1526408529507-d4ab4fb0f3cb?w=400&h=300&fit=crop'
    ],
    [
        'nome' => 'Câmera Mirrorless',
        'descricao' => 'Câmera profissional com sensor full frame e 8K de resolução',
        'preco' => 7999.99,
        'estoque' => 5,
        'imagem' => 'https://images.unsplash.com/photo-1612198188060-c7c2a3b66eae?w=400&h=300&fit=crop'
    ],
    [
        'nome' => 'Smart Watch Elite',
        'descricao' => 'Relógio inteligente com GPS, monitor cardíaco e 7 dias de bateria',
        'preco' => 1299.99,
        'estoque' => 20,
        'imagem' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&h=300&fit=crop'
    ],
    [
        'nome' => 'Monitor 4K',
        'descricao' => 'Monitor 27" 4K com HDR e taxa de atualização 144Hz',
        'preco' => 1899.99,
        'estoque' => 10,
        'imagem' => 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=400&h=300&fit=crop'
    ],
    [
        'nome' => 'Teclado Mecânico',
        'descricao' => 'Teclado mecânico RGB com switches personalizáveis',
        'preco' => 399.99,
        'estoque' => 25,
        'imagem' => 'https://images.unsplash.com/photo-1587829191301-441f6e9e7e3d?w=400&h=300&fit=crop'
    ],
    [
        'nome' => 'Mouse Gamer',
        'descricao' => 'Mouse com sensor 16000 DPI e 8 botões programáveis',
        'preco' => 199.99,
        'estoque' => 40,
        'imagem' => 'https://images.unsplash.com/photo-1527814050087-3793815479db?w=400&h=300&fit=crop'
    ],
    [
        'nome' => 'Carregador Rápido',
        'descricao' => 'Carregador USB-C 65W com carga rápida para múltiplos dispositivos',
        'preco' => 149.99,
        'estoque' => 50,
        'imagem' => 'https://images.unsplash.com/photo-1625948515291-69613efd103f?w=400&h=300&fit=crop'
    ]
];

$adicionados = 0;

foreach ($produtos as $p) {
    $stmt = $conn->prepare("INSERT INTO produtos (nome, descricao, preco, estoque, imagem) VALUES (?, ?, ?, ?, ?)");
    if ($stmt !== false) {
        $stmt->bind_param("ssdis", $p['nome'], $p['descricao'], $p['preco'], $p['estoque'], $p['imagem']);
        if ($stmt->execute()) {
            $adicionados++;
        }
    }
}

echo "<div style='text-align: center; padding: 2rem; font-size: 1.2rem;'>";
echo "<h1>✅ $adicionados produtos adicionados com sucesso!</h1>";
echo "<p><a href='index.php' style='color: #2563eb; text-decoration: none;'>Voltar para a loja</a></p>";
echo "<p style='color: #666; margin-top: 1rem;'>Agora pode deletar este arquivo: <strong>popular_produtos.php</strong></p>";
echo "</div>";
?>
