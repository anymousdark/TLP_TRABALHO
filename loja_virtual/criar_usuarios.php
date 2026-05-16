<?php
require_once 'config.php';

echo "<h1>👤 Criando Usuários...</h1>";

// Verificar e deletar usuários antigos se existirem (opcional)
// $conn->query("DELETE FROM usuarios");

$usuarios = [
    [
        'nome' => 'Administrador TechStore',
        'email' => 'admin@techstore.com',
        'senha' => 'admin123',
        'tipo' => 'admin'
    ],
    [
        'nome' => 'Cliente Teste',
        'email' => 'cliente@gmail.com',
        'senha' => '123456',
        'tipo' => 'cliente'
    ]
];

$criados = 0;
$erros = [];

foreach ($usuarios as $u) {
    // Verificar se já existe
    $check = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    if ($check !== false) {
        $check->bind_param("s", $u['email']);
        $check->execute();
        $result = $check->get_result();
        
        if ($result->num_rows > 0) {
            echo "<p style='color: orange;'>⚠️ " . $u['email'] . " já existe</p>";
            continue;
        }
    }
    
    // Criar novo usuário
    $hash = password_hash($u['senha'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)");
    
    if ($stmt !== false) {
        $stmt->bind_param("ssss", $u['nome'], $u['email'], $hash, $u['tipo']);
        if ($stmt->execute()) {
            echo "<p style='color: green;'>✅ " . $u['email'] . " criado com sucesso!</p>";
            echo "<p style='margin-left: 1rem; color: #666;'>📝 Tipo: " . $u['tipo'] . " | Senha: " . $u['senha'] . "</p>";
            $criados++;
        } else {
            $erros[] = $u['email'] . ": " . $stmt->error;
        }
    } else {
        $erros[] = "Erro ao preparar: " . $conn->error;
    }
}

echo "<div style='background: #dcfce7; border: 2px solid #22c55e; padding: 1.5rem; border-radius: 0.5rem; margin-top: 2rem; text-align: center;'>";
echo "<h2 style='color: #166534;'>🎉 " . $criados . " usuário(s) pronto(s)!</h2>";

if (!empty($erros)) {
    echo "<div style='color: red; margin-top: 1rem;'>";
    foreach ($erros as $e) {
        echo "<p>❌ $e</p>";
    }
    echo "</div>";
}

echo "<p style='margin-top: 1.5rem;'><a href='login.php' style='color: #2563eb; text-decoration: none; font-weight: bold; font-size: 1.1rem;'>👉 Ir para Login</a></p>";
echo "<p style='color: #666; margin-top: 1rem;'>Agora pode deletar este arquivo: <strong>criar_usuarios.php</strong></p>";
echo "</div>";
?>
