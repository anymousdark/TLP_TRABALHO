<?php
require_once 'config.php';

$usuarios = [
    ['nome' => 'Administrador Lakastech', 'email' => 'admin@lakastech.com', 'senha' => 'admin123', 'tipo' => 'admin'],
    ['nome' => 'Cliente Teste', 'email' => 'cliente@gmail.com', 'senha' => '123456', 'tipo' => 'cliente']
];

$criados = 0;
$erros = [];

foreach ($usuarios as $u) {
    $check = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    if ($check !== false) {
        $check->bind_param("s", $u['email']);
        $check->execute();
        if ($check->get_result()->num_rows > 0) continue;
    }

    $hash = password_hash($u['senha'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)");
    if ($stmt !== false) {
        $stmt->bind_param("ssss", $u['nome'], $u['email'], $hash, $u['tipo']);
        if ($stmt->execute()) $criados++;
        else $erros[] = $u['email'] . ": " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Usuarios - Lakastech</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-body">
        <div class="login-container" style="text-align: center;">
            <h1><?php echo $criados; ?> usuario(s) criado(s) com sucesso!</h1>
            <?php if (!empty($erros)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($erros as $e) echo "<p>$e</p>"; ?>
                </div>
            <?php endif; ?>
            <div class="alert alert-success" style="text-align: left;">
                <p><strong>Admin:</strong> admin@lakastech.com / admin123</p>
                <p><strong>Cliente:</strong> cliente@gmail.com / 123456</p>
            </div>
            <a href="login.php" class="btn btn-primary">Ir para Login</a>
            <p class="text-muted" style="margin-top: 1rem;">Deletar este arquivo apos uso</p>
        </div>
    </div>
</body>
</html>
