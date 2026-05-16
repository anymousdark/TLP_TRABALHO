<?php
require_once 'config.php';

echo "<h1>Criando Banco de Dados...</h1>";

$sql_file = file_get_contents('database.sql');
// Preservar consultas com ";" dentro de strings não é trivial aqui;
// mas para este projeto o formato do database.sql funciona.
$queries = array_filter(explode(';', $sql_file));

// Atualização defensiva: se faltar coluna estoque na tabela produtos, adiciona.
$col_check = $conn->query("SHOW COLUMNS FROM produtos LIKE 'estoque'");
if ($col_check !== false && $col_check->num_rows === 0) {
    $conn->query("ALTER TABLE produtos ADD COLUMN estoque INT NOT NULL DEFAULT 0");
}


$sucesso = 0;
$erro_count = 0;

foreach ($queries as $query) {
    $query = trim($query);
    if (!empty($query)) {
        if ($conn->query($query)) {
            $sucesso++;
        } else {
            echo "<p style='color: red;'>❌ Erro: " . $conn->error . "</p>";
            $erro_count++;
        }
    }
}

echo "<div style='text-align: center; padding: 2rem; font-size: 1.1rem;'>";
echo "<h2>✅ Banco de dados criado com sucesso!</h2>";
echo "<p>Queries executadas: $sucesso</p>";
if ($erro_count > 0) {
    echo "<p style='color: red;'>Erros encontrados: $erro_count (ignoráveis se for 'já existe')</p>";
}
echo "<p style='margin-top: 1rem;'><a href='index.php' style='color: #2563eb; text-decoration: none; font-weight: bold;'>👉 Ir para a Loja</a></p>";
echo "<p style='color: #666; margin-top: 1rem;'>Agora pode deletar este arquivo: <strong>setup_db.php</strong></p>";
echo "</div>";
?>
