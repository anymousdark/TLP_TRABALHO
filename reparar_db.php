<?php
require_once 'config.php';

echo "<h1>🔧 Reparando o Banco de Dados...</h1>";

// Verificar se a coluna 'tipo' existe
$check = $conn->query("SHOW COLUMNS FROM usuarios LIKE 'tipo'");

if ($check === false) {
    echo "<p style='color: red;'>❌ Erro ao verificar tabela: " . $conn->error . "</p>";
} elseif ($check->num_rows == 0) {
    // Coluna não existe, adicionar
    echo "<p>⚠️ Coluna 'tipo' não encontrada, adicionando...</p>";
    
    if ($conn->query("ALTER TABLE usuarios ADD COLUMN tipo ENUM('cliente', 'admin') DEFAULT 'cliente'")) {
        echo "<p style='color: green;'>✅ Coluna 'tipo' adicionada com sucesso!</p>";
    } else {
        echo "<p style='color: red;'>❌ Erro ao adicionar coluna: " . $conn->error . "</p>";
    }
} else {
    echo "<p style='color: green;'>✅ Coluna 'tipo' já existe!</p>";
}

// Verificar se existem usuários
$users_check = $conn->query("SELECT COUNT(*) as total FROM usuarios");
$users = $users_check->fetch_assoc();

if ($users['total'] == 0) {
    echo "<p>⚠️ Nenhum usuário encontrado, adicionando usuários padrão...</p>";
    
    // Admin
    $admin_pass = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)");
    if ($stmt !== false) {
        $tipo_admin = 'admin';
        $stmt->bind_param("ssss", $nome, $email, $admin_pass, $tipo_admin);
        $nome = "Administrador";
        $email = "admin@techstore.com";
        if ($stmt->execute()) {
            echo "<p style='color: green;'>✅ Admin criado: admin@techstore.com / admin123</p>";
        } else {
            echo "<p style='color: red;'>❌ Erro ao criar admin: " . $stmt->error . "</p>";
        }
    }
    
    // Cliente
    $cliente_pass = password_hash('123456', PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)");
    if ($stmt !== false) {
        $tipo_cliente = 'cliente';
        $stmt->bind_param("ssss", $nome2, $email2, $cliente_pass, $tipo_cliente);
        $nome2 = "Cliente Teste";
        $email2 = "cliente@gmail.com";
        if ($stmt->execute()) {
            echo "<p style='color: green;'>✅ Cliente criado: cliente@gmail.com / 123456</p>";
        } else {
            echo "<p style='color: red;'>❌ Erro ao criar cliente: " . $stmt->error . "</p>";
        }
    }
} else {
    echo "<p style='color: green;'>✅ Usuários já existem na base!</p>";
}

echo "<div style='background: #dcfce7; border: 2px solid #22c55e; padding: 1.5rem; border-radius: 0.5rem; margin-top: 2rem; text-align: center;'>";
echo "<h2 style='color: #166534;'>🎉 Banco de Dados Reparado!</h2>";
echo "<p><a href='index.php' style='color: #2563eb; text-decoration: none; font-weight: bold; font-size: 1.1rem;'>👉 Voltar para a Loja</a></p>";
echo "<p style='color: #666; margin-top: 1rem;'>Agora pode deletar este arquivo: <strong>reparar_db.php</strong></p>";
echo "</div>";
?>
