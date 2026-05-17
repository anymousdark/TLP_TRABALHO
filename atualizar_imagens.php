<?php
require_once 'config.php';

// Mapear produtos para imagens locais
$mapa = [
    'Smartphone Galaxy S23' => 'uploads/smartphone.jpg',
    'Notebook Dell XPS 13' => 'uploads/notebook.jpg',
    'Monitor LG UltraWide' => 'uploads/monitor.jpg',
    'Teclado Mecanico RGB' => 'uploads/teclado.jpg',
    'Mouse Gamer Logi' => 'uploads/mouse.jpg',
    'PC Gamer Elite Edition' => 'uploads/pc-gamer.jpg',
    'Corsair CV650 80 Plus' => 'uploads/fonte.jpg',
    'HyperX Cloud II 7.1' => 'uploads/fone.jpg',
    'Razer BlackWidow V3' => 'uploads/teclado.jpg',
    'Logitech G502 HERO' => 'uploads/mouse.jpg',
    'Monitor LG UltraGear 27"' => 'uploads/monitor.jpg',
    'Samsung 980 1TB NVMe' => 'uploads/ssd.jpg',
    'Corsair Vengeance 16GB' => 'uploads/ssd.jpg',
    'AMD Ryzen 7 5800X' => 'uploads/processador.jpg',
    'NVIDIA RTX 4070 Super' => 'uploads/pc-gamer.jpg',
    'Smartphone Pro X' => 'uploads/smartphone.jpg',
    'Laptop Ultra' => 'uploads/notebook.jpg',
    'Fone Bluetooth Premium' => 'uploads/fone.jpg',
    'Tablet 10.5"' => 'uploads/tablet.jpg',
    'Camera Mirrorless' => 'uploads/camera.jpg',
    'Smart Watch Elite' => 'uploads/smartwatch.jpg',
    'Monitor 4K' => 'uploads/monitor.jpg',
    'Carregador Rapido' => 'uploads/fonte.jpg'
];

$atualizados = 0;
$erros = [];

foreach ($mapa as $nome => $imagem) {
    $stmt = $conn->prepare("UPDATE produtos SET imagem = ? WHERE nome = ?");
    if ($stmt !== false) {
        $stmt->bind_param("ss", $imagem, $nome);
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $atualizados++;
        }
    }
}

// Produtos sem nome exato - atualizar por LIKE
$like_map = [
    '%Ryzen 7 5800X%' => 'uploads/processador.jpg',
    '%Ryzen 5 5600%' => 'uploads/processador.jpg',
    '%RTX 4060 Ti%' => 'uploads/pc-gamer.jpg',
    '%AMD Ryzen%' => 'uploads/processador.jpg',
    '%NVIDIA RTX%' => 'uploads/pc-gamer.jpg',
];

foreach ($like_map as $like => $imagem) {
    $stmt = $conn->prepare("UPDATE produtos SET imagem = ? WHERE nome LIKE ? AND (imagem IS NULL OR imagem = '' OR imagem LIKE '%http%')");
    if ($stmt !== false) {
        $stmt->bind_param("ss", $imagem, $like);
        $stmt->execute();
    }
}

echo "<!DOCTYPE html><html lang='pt-ao'><head><link rel='stylesheet' href='style.css'></head><body>";
echo "<div class='login-body'><div class='login-container' style='text-align: center;'>";
echo "<h1>$atualizados produtos atualizados com imagens locais!</h1>";
echo "<p>Imagens em: <strong>uploads/</strong></p>";
echo "<br><a href='index.php' class='btn btn-primary'>Ver Loja</a>";
echo "</div></div></body></html>";
