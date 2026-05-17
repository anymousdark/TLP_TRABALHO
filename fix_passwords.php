<?php
require_once 'config.php';

echo "<h1>Atualizando Senhas...</h1>";

$usuarios = [
    ['email' => 'admin@lakastech.com', 'senha' => 'admin123'],
    ['email' => 'cliente@gmail.com', 'senha' => '123456']
];

foreach ($usuarios as $u) {
    $hash = password_hash($u['senha'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE usuarios SET senha = ? WHERE email = ?");
    if ($stmt === false) {
        echo "Erro ao preparar UPDATE: " . $conn->error . "<br>";
    } else {
        $stmt->bind_param("ss", $hash, $u['email']);
        if ($stmt->execute()) {
            echo "Senha de " . $u['email'] . " atualizada com sucesso!<br>";
        }
    }
}

echo "<br><a href='login.php'>Ir para Login</a>";
unlink(__FILE__); // Deleta este arquivo após execução por segurança
?>
