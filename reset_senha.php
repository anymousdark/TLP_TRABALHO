<?php
require_once 'config.php';

// Hashes bcrypt válidos
// admin123
$admin_hash = password_hash('admin123', PASSWORD_DEFAULT);
// 123456
$cliente_hash = password_hash('123456', PASSWORD_DEFAULT);

echo "<h1>🔄 Atualizando Senhas...</h1>";

// Atualizar Admin
$stmt = $conn->prepare("UPDATE usuarios SET senha = ? WHERE email = ?");
if ($stmt !== false) {
    $email = 'admin@lakastech.com';
    $stmt->bind_param("ss", $admin_hash, $email);
    if ($stmt->execute()) {
        echo "<p style='color: green;'>✅ Senha do admin atualizada!</p>";
    } else {
        echo "<p style='color: red;'>❌ Erro ao atualizar admin: " . $stmt->error . "</p>";
    }
}

// Atualizar Cliente
$stmt = $conn->prepare("UPDATE usuarios SET senha = ? WHERE email = ?");
if ($stmt !== false) {
    $email = 'cliente@gmail.com';
    $stmt->bind_param("ss", $cliente_hash, $email);
    if ($stmt->execute()) {
        echo "<p style='color: green;'>✅ Senha do cliente atualizada!</p>";
    } else {
        echo "<p style='color: red;'>❌ Erro ao atualizar cliente: " . $stmt->error . "</p>";
    }
}

echo "<div style='background: #dcfce7; border: 2px solid #22c55e; padding: 1.5rem; border-radius: 0.5rem; margin-top: 2rem; text-align: center;'>";
echo "<h2 style='color: #166534;'>✅ Senhas Resetadas!</h2>";
echo "<p>Admin: <strong>admin@lakastech.com</strong> / <strong>admin123</strong></p>";
echo "<p>Cliente: <strong>cliente@gmail.com</strong> / <strong>123456</strong></p>";
echo "<p style='margin-top: 1.5rem;'><a href='login.php' style='color: #2563eb; text-decoration: none; font-weight: bold; font-size: 1.1rem;'>👉 Ir para Login</a></p>";
echo "</div>";
?>
