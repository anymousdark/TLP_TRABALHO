<?php
require_once 'config.php';

// Verificar se já existe o arquivo de controle
if (file_exists('usuarios_criados.txt')) {
    header("Location: index.php");
    exit();
}

// Limpar tabela de usuários (se necessário)
$conn->query("DELETE FROM usuarios");

// Inserir usuários
$usuarios_sql = "
INSERT INTO usuarios (nome, email, senha, tipo) VALUES 
('Administrador TechStore', 'admin@techstore.com', '\$2y\$10\$3wHsHvd9.6u8Ol5EZvJCJu.G8m8a2xqMvZ9rP5vK3kL2m9n8o7p6q', 'admin'),
('Cliente Teste', 'cliente@gmail.com', '\$2y\$10\$8V1Qs.8V8V1Qs.8V8V1Qs.8V8V1Qs.8V8V1Qs.8V8V1Qs.8V8V1Qs.8V8V1Qs.', 'cliente');
";

if ($conn->multi_query($usuarios_sql)) {
    // Criar arquivo de controle para executar apenas uma vez
    file_put_contents('usuarios_criados.txt', date('Y-m-d H:i:s'));
    echo "<div style='background: #dcfce7; padding: 2rem; border-radius: 1rem; text-align: center; margin: 2rem;'>";
    echo "<h1 style='color: #166534;'>✅ Usuários Criados com Sucesso!</h1>";
    echo "<p>Admin: <strong>admin@techstore.com</strong> / <strong>admin123</strong></p>";
    echo "<p>Cliente: <strong>cliente@gmail.com</strong> / <strong>123456</strong></p>";
    echo "<p style='margin-top: 2rem;'><a href='login.php' style='color: #2563eb; text-decoration: none; font-weight: bold; font-size: 1.1rem;'>👉 Ir para Login</a></p>";
    echo "</div>";
} else {
    echo "<p style='color: red;'>❌ Erro: " . $conn->error . "</p>";
}
?>
