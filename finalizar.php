<?php 
require_once 'config.php'; 

if (!isset($_SESSION['carrinho']) || empty($_SESSION['carrinho'])) {
    header("Location: index.php");
    exit();
}

// Verificar se usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$conn->begin_transaction();

try {
    $usuario_id = $_SESSION['usuario_id']; 
    
    // Calcular total e verificar estoque novamente antes de fechar
    $total = 0;
    $itens_para_processar = [];
    
    foreach ($_SESSION['carrinho'] as $id => $qtd) {
        $stmt = $conn->prepare("SELECT * FROM produtos WHERE id = ? FOR UPDATE");
        if ($stmt === false) {
            throw new Exception("Erro ao preparar SELECT: " . $conn->error);
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $prod = $res->fetch_assoc();
        
        if ($prod['estoque'] < $qtd) {
            throw new Exception("Estoque insuficiente para o produto: " . $prod['nome']);
        }
        
        $subtotal = $prod['preco'] * $qtd;
        $total += $subtotal;
        $itens_para_processar[] = [
            'id' => $id,
            'qtd' => $qtd,
            'preco' => $prod['preco']
        ];
    }
    
    // Criar pedido
    $stmt = $conn->prepare("INSERT INTO pedidos (usuario_id, total) VALUES (?, ?)");
    if ($stmt === false) {
        throw new Exception("Erro ao preparar INSERT pedido: " . $conn->error);
    }
    $stmt->bind_param("id", $usuario_id, $total);
    $stmt->execute();
    $pedido_id = $conn->insert_id;
    
    // Inserir itens e reduzir estoque
    foreach ($itens_para_processar as $item) {
        // Inserir item_pedido
        $stmt = $conn->prepare("INSERT INTO itens_pedido (pedido_id, produto_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?)");
        if ($stmt === false) {
            throw new Exception("Erro ao preparar INSERT item: " . $conn->error);
        }
        $stmt->bind_param("iiid", $pedido_id, $item['id'], $item['qtd'], $item['preco']);
        $stmt->execute();
        
        // Reduzir estoque
        $stmt = $conn->prepare("UPDATE produtos SET estoque = estoque - ? WHERE id = ?");
        if ($stmt === false) {
            throw new Exception("Erro ao preparar UPDATE estoque: " . $conn->error);
        }
        $stmt->bind_param("ii", $item['qtd'], $item['id']);
        $stmt->execute();
    }
    
    $conn->commit();
    unset($_SESSION['carrinho']);
    
    require_once 'header.php';
    echo "<div class='container' style='text-align: center;'>
            <div style='font-size: 4rem; margin-bottom: 1rem;'>✅</div>
            <h1>Pedido Realizado com Sucesso!</h1>
            <p>Obrigado por comprar conosco. Seu pedido #$pedido_id foi processado.</p>
            <br>
            <a href='pedidos.php' class='btn btn-primary'>Ver Meus Pedidos</a>
          </div>";
    require_once 'footer.php';
    
} catch (Exception $e) {
    $conn->rollback();
    require_once 'header.php';
    echo "<div class='container'>
            <h1>Erro ao processar pedido</h1>
            <p class='btn btn-danger' style='display: block;'>" . $e->getMessage() . "</p>
            <br>
            <a href='carrinho.php' class='btn btn-primary'>Voltar ao Carrinho</a>
          </div>";
    require_once 'footer.php';
}
?>
