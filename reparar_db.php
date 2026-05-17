<?php
require_once 'config.php';

$mensagens = [];

$check = $conn->query("SHOW COLUMNS FROM usuarios LIKE 'tipo'");
if ($check === false) {
    $mensagens[] = ['tipo' => 'danger', 'texto' => 'Erro ao verificar tabela: ' . $conn->error];
} elseif ($check->num_rows == 0) {
    if ($conn->query("ALTER TABLE usuarios ADD COLUMN tipo ENUM('cliente', 'admin') DEFAULT 'cliente'")) {
        $mensagens[] = ['tipo' => 'success', 'texto' => "Coluna 'tipo' adicionada!"];
    } else {
        $mensagens[] = ['tipo' => 'danger', 'texto' => 'Erro: ' . $conn->error];
    }
} else {
    $mensagens[] = ['tipo' => 'success', 'texto' => "Coluna 'tipo' ja existe!"];
}

$users_check = $conn->query("SELECT COUNT(*) as total FROM usuarios");
$users = $users_check->fetch_assoc();

if ($users['total'] == 0) {
    $admin_pass = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)");
    if ($stmt !== false) {
        $tipo = 'admin';
        $nome = "Administrador";
        $email = "admin@lakastech.com";
        $stmt->bind_param("ssss", $nome, $email, $admin_pass, $tipo);
        if ($stmt->execute()) {
            $mensagens[] = ['tipo' => 'success', 'texto' => 'Admin criado: admin@lakastech.com / admin123'];
        }
    }

    $cliente_pass = password_hash('123456', PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)");
    if ($stmt !== false) {
        $tipo = 'cliente';
        $nome = "Cliente Teste";
        $email = "cliente@gmail.com";
        $stmt->bind_param("ssss", $nome, $email, $cliente_pass, $tipo);
        if ($stmt->execute()) {
            $mensagens[] = ['tipo' => 'success', 'texto' => 'Cliente criado: cliente@gmail.com / 123456'];
        }
    }
} else {
    $mensagens[] = ['tipo' => 'success', 'texto' => 'Usuarios ja existem na base!'];
}
?>
<!DOCTYPE html>
<html lang="pt-ao">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reparar BD - Lakastech</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-body">
        <div class="login-container">
            <h1>Reparar Banco de Dados</h1>
            <?php foreach ($mensagens as $m): ?>
                <div class="alert alert-<?php echo $m['tipo']; ?>"><?php echo $m['texto']; ?></div>
            <?php endforeach; ?>
            <div style="text-align: center; margin-top: 1.5rem;">
                <a href="index.php" class="btn btn-primary">Voltar para Loja</a>
                <p class="text-muted" style="margin-top: 1rem;">Deletar este arquivo apos uso</p>
            </div>
        </div>
    </div>
</body>
</html>
