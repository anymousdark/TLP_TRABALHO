<?php
require_once 'config.php';

if (file_exists('usuarios_criados.txt')) {
    header("Location: index.php");
    exit();
}

$conn->query("DELETE FROM usuarios");

$usuarios_sql = "
INSERT INTO usuarios (nome, email, senha, tipo) VALUES 
('Administrador Lakastech', 'admin@lakastech.com', '\$2y\$10\$3wHsHvd9.6u8Ol5EZvJCJu.G8m8a2xqMvZ9rP5vK3kL2m9n8o7p6q', 'admin'),
('Cliente Teste', 'cliente@gmail.com', '\$2y\$10\$8V1Qs.8V8V1Qs.8V8V1Qs.8V8V1Qs.8V8V1Qs.8V8V1Qs.8V8V1Qs.8V8V1Qs.', 'cliente');
";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicializar - Lakastech</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-body">
        <div class="login-container" style="text-align: center;">
<?php
if ($conn->multi_query($usuarios_sql)) {
    file_put_contents('usuarios_criados.txt', date('Y-m-d H:i:s'));
    echo "<h1>Usuarios Criados com Sucesso!</h1>";
    echo "<div class='alert alert-success' style='text-align: left;'>";
    echo "<p><strong>Admin:</strong> admin@lakastech.com / admin123</p>";
    echo "<p><strong>Cliente:</strong> cliente@gmail.com / 123456</p>";
    echo "</div>";
    echo "<a href='login.php' class='btn btn-primary'>Ir para Login</a>";
} else {
    echo "<div class='alert alert-danger'>Erro: " . $conn->error . "</div>";
}
?>
        </div>
    </div>
</body>
</html>
