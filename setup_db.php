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

echo "<div class='container' style='text-align: center;'>
        <h2>Banco de dados criado com sucesso!</h2>
        <p>Queries executadas: $sucesso</p>";
if ($erro_count > 0) {
    echo "<div class='alert alert-danger'>Erros encontrados: $erro_count (ignoraveis se for 'ja existe')</div>";
}
echo "<p style='margin-top: 1.5rem;'><a href='index.php' class='btn btn-primary'>Ir para a Loja</a></p>
        <p class='text-muted' style='margin-top: 1rem;'>Agora pode deletar este arquivo: <strong>setup_db.php</strong></p>
      </div>";
?>
