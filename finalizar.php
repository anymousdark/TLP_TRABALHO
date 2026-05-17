<?php 
require_once 'config.php'; 

if (!isset($_SESSION['carrinho']) || empty($_SESSION['carrinho'])) {
    header("Location: index.php");
    exit();
}

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Processar etapa de revisao (salvar na sessao)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['etapa']) && $_POST['etapa'] === 'revisar') {
    $_SESSION['checkout'] = [
        'metodo_pagamento' => $_POST['metodo_pagamento'] ?? '',
        'endereco' => trim($_POST['endereco'] ?? ''),
        'bairro' => trim($_POST['bairro'] ?? ''),
        'municipio' => trim($_POST['municipio'] ?? ''),
        'telefone' => trim($_POST['telefone'] ?? '')
    ];
    header("Location: finalizar.php?etapa=revisar");
    exit();
}

require_once 'header.php';

$animais = '<div class="animal-corner animal-corner-bl">' . animal_girafa(65) . '</div>';
$animais .= '<div class="animal-corner animal-corner-tr">' . animal_zebra(65) . '</div>';

// Calcular total e carregar itens
$total = 0;
$itens = [];
if (!empty($_SESSION['carrinho'])) {
    $ids = implode(',', array_keys($_SESSION['carrinho']));
    $result = $conn->query("SELECT * FROM produtos WHERE id IN ($ids)");
    while ($row = $result->fetch_assoc()) {
        $row['quantidade'] = $_SESSION['carrinho'][$row['id']];
        $row['subtotal'] = $row['preco'] * $row['quantidade'];
        $total += $row['subtotal'];
        $itens[] = $row;
    }
}

// Carregar metodos de pagamento da loja (base de dados)
$metodos_pagamento = [];
$stmt_mp = $conn->query("SELECT * FROM metodos_pagamento WHERE ativo = 1 ORDER BY id ASC");
while ($mp = $stmt_mp->fetch_assoc()) {
    $metodos_pagamento[$mp['id']] = $mp;
}

$etapa = $_POST['etapa'] ?? $_GET['etapa'] ?? 'dados';
?>

<style>
.checkout-step { display: none; }
.checkout-step.active { display: block; }
.step-indicator {
    display: flex;
    justify-content: center;
    gap: 0;
    margin-bottom: 2.5rem;
    position: relative;
}
.step-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1.25rem;
    font-size: 0.85rem;
    font-weight: 500;
    color: var(--text-light);
    position: relative;
    z-index: 1;
}
.step-item .step-num {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    font-weight: 600;
    background: var(--border);
    color: var(--text-light);
    transition: all 0.3s;
}
.step-item.active .step-num { background: var(--primary); color: white; }
.step-item.done .step-num { background: var(--success); color: white; }
.step-item.active { color: var(--text); }
.step-item.done { color: var(--text); }
.step-line {
    flex: 1;
    height: 2px;
    background: var(--border);
    align-self: center;
    max-width: 60px;
}
.step-line.done { background: var(--success); }

.payment-option {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.25rem;
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    margin-bottom: 0.75rem;
    cursor: pointer;
    transition: all 0.2s;
    background: var(--background);
}
.payment-option:hover { border-color: var(--primary); }
.payment-option.selected { border-color: var(--primary); background: rgba(0, 113, 227, 0.05); }
.payment-option input[type="radio"] { accent-color: var(--primary); width: 18px; height: 18px; cursor: pointer; }
.payment-option .payment-info { flex: 1; }
.payment-option .payment-info strong { display: block; font-size: 0.95rem; }
.payment-option .payment-info small { color: var(--text-light); font-size: 0.8rem; }
.payment-details {
    display: none;
    padding: 1rem 1.25rem;
    background: var(--card);
    border-radius: var(--radius-md);
    border: 1px solid var(--border);
    margin-top: -0.4rem;
    margin-bottom: 0.75rem;
    font-size: 0.9rem;
}
.payment-details.show { display: block; }
.payment-details span { display: block; margin-bottom: 0.25rem; }
.payment-details .label { color: var(--text-light); font-size: 0.8rem; }
.payment-details .value { font-weight: 600; }

.review-card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    margin-bottom: 1rem;
}
.review-card h3 {
    font-size: 1rem;
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.review-row {
    display: flex;
    justify-content: space-between;
    padding: 0.4rem 0;
    font-size: 0.9rem;
    border-bottom: 1px solid var(--border);
}
.review-row:last-child { border-bottom: none; }
.review-row .label { color: var(--text-light); }
.review-row .value { font-weight: 500; text-align: right; }
</style>

<div class="container" style="max-width: 700px;">

    <!-- Indicador de etapas -->
    <?php
    $etapas = ['dados' => 1, 'revisar' => 2, 'confirmado' => 3];
    $etapa_atual = $etapas[$etapa] ?? 1;
    ?>
    <div class="step-indicator">
        <div class="step-item <?php echo $etapa_atual >= 1 ? ($etapa_atual > 1 ? 'done' : 'active') : ''; ?>">
            <span class="step-num"><?php echo $etapa_atual > 1 ? '<i class="fas fa-check"></i>' : '1'; ?></span>
            <span>Dados</span>
        </div>
        <div class="step-line <?php echo $etapa_atual > 1 ? 'done' : ''; ?>"></div>
        <div class="step-item <?php echo $etapa_atual == 2 ? 'active' : ($etapa_atual > 2 ? 'done' : ''); ?>">
            <span class="step-num"><?php echo $etapa_atual > 2 ? '<i class="fas fa-check"></i>' : '2'; ?></span>
            <span>Revisar</span>
        </div>
        <div class="step-line <?php echo $etapa_atual > 2 ? 'done' : ''; ?>"></div>
        <div class="step-item <?php echo $etapa_atual >= 3 ? 'active' : ''; ?>">
            <span class="step-num">3</span>
            <span>Confirmado</span>
        </div>
    </div>

    <?php if ($etapa === 'dados'): ?>
        <!-- ETAPA 1: Dados de pagamento e entrega -->
        <h2>Dados para Finalizar a Compra</h2>
        <p style="color: var(--text-light); margin-bottom: 1.5rem;">Escolha o metodo de pagamento e informe onde entregar.</p>

        <form method="post" id="form-dados">
            <input type="hidden" name="etapa" value="revisar">

            <!-- Metodo de Pagamento -->
            <div class="section-card">
                <h3><i class="fas fa-credit-card"></i> Metodo de Pagamento</h3>
                <p style="font-size: 0.85rem; color: var(--text-light); margin-bottom: 1rem;">
                    Escolha para onde fazer a transferencia.
                </p>

                <?php $primeiro = true; foreach ($metodos_pagamento as $chave => $metodo): 
                    $sel = $primeiro || ($_SESSION['checkout']['metodo_pagamento'] ?? '') === $chave;
                    if ($sel) $primeiro = false;
                ?>
                <label class="payment-option <?php echo $sel ? 'selected' : ''; ?>">
                    <input type="radio" name="metodo_pagamento" value="<?php echo $chave; ?>" <?php echo $sel ? 'checked' : ''; ?> required>
                    <div class="payment-info">
                        <strong><?php echo $metodo['nome']; ?></strong>
                        <small><?php echo $metodo['descricao']; ?></small>
                    </div>
                </label>
                <?php endforeach; ?>

                <p style="font-size: 0.8rem; color: var(--text-light); margin-top: 0.75rem;">
                    <i class="fas fa-info-circle"></i> Os dados da conta para transferencia serao exibidos apos a confirmacao do pedido.
                </p>
            </div>

            <!-- Endereco de Entrega -->
            <div class="section-card">
                <h3><i class="fas fa-map-marker-alt"></i> Local de Entrega</h3>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 500; margin-bottom: 0.35rem;">Endereco completo <span style="color: var(--danger);">*</span></label>
                    <input type="text" name="endereco" placeholder="Rua, numero, ponto de referencia..." value="<?php echo htmlspecialchars($_SESSION['checkout']['endereco'] ?? ''); ?>" required style="width: 100%;">
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 500; margin-bottom: 0.35rem;">Bairro <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="bairro" placeholder="Seu bairro" value="<?php echo htmlspecialchars($_SESSION['checkout']['bairro'] ?? ''); ?>" required style="width: 100%;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 500; margin-bottom: 0.35rem;">Municipio <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="municipio" placeholder="Luanda, Benguela..." value="<?php echo htmlspecialchars($_SESSION['checkout']['municipio'] ?? ''); ?>" required style="width: 100%;">
                    </div>
                </div>
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 500; margin-bottom: 0.35rem;">Telefone para contato <span style="color: var(--danger);">*</span></label>
                    <input type="tel" name="telefone" placeholder="+244 900 000 000" value="<?php echo htmlspecialchars($_SESSION['checkout']['telefone'] ?? ''); ?>" required style="width: 100%;">
                </div>
            </div>

            <!-- Resumo do carrinho -->
            <div class="section-card">
                <h3><i class="fas fa-shopping-cart"></i> Resumo do Pedido</h3>
                <table style="width: 100%; font-size: 0.9rem;">
                    <tbody>
                        <?php foreach ($itens as $item): ?>
                        <tr>
                            <td style="padding: 0.35rem 0;"><?php echo $item['quantidade']; ?>x</td>
                            <td style="padding: 0.35rem 0;"><?php echo htmlspecialchars($item['nome']); ?></td>
                            <td style="padding: 0.35rem 0; text-align: right;">Kz <?php echo number_format($item['subtotal'], 2, ',', '.'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="total-section" style="font-size: 1.1rem; margin-top: 0.5rem;">Total: Kz <?php echo number_format($total, 2, ',', '.'); ?></div>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: space-between;">
                <a href="carrinho.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar ao Carrinho</a>
                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem;">
                    Continuar <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </form>

        <script>
        document.querySelectorAll('.payment-option input[type="radio"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                document.querySelectorAll('.payment-option').forEach(function(o) { o.classList.remove('selected'); });
                this.closest('.payment-option').classList.add('selected');
            });
        });
        </script>

    <?php elseif ($etapa === 'revisar'): ?>
        <!-- ETAPA 2: Revisao -->
        <?php
        $checkout = $_SESSION['checkout'] ?? null;
        if (!$checkout) {
            header("Location: finalizar.php");
            exit();
        }
        $metodo_sel = $metodos_pagamento[$checkout['metodo_pagamento']] ?? null;
        $endereco_completo = $checkout['endereco'];
        if ($checkout['bairro']) $endereco_completo .= ', ' . $checkout['bairro'];
        if ($checkout['municipio']) $endereco_completo .= ', ' . $checkout['municipio'];
        ?>

        <h2>Revisar Informacoes</h2>
        <p style="color: var(--text-light); margin-bottom: 1.5rem;">Confira todos os dados antes de confirmar o pedido.</p>

        <div class="review-card">
            <h3><i class="fas fa-credit-card" style="color: var(--primary);"></i> Metodo de Pagamento</h3>
            <div class="review-row">
                <span class="label">Metodo</span>
                <span class="value"><?php echo $metodo_sel ? $metodo_sel['nome'] : $checkout['metodo_pagamento']; ?></span>
            </div>
            <?php if ($metodo_sel): ?>
            <div class="review-row">
                <span class="label">Titular</span>
                <span class="value"><?php echo $metodo_sel['titular']; ?></span>
            </div>
            <div class="review-row">
                <span class="label">Conta</span>
                <span class="value"><?php echo $metodo_sel['conta']; ?></span>
            </div>
            <?php endif; ?>
        </div>

        <div class="review-card">
            <h3><i class="fas fa-map-marker-alt" style="color: var(--primary);"></i> Local de Entrega</h3>
            <div class="review-row">
                <span class="label">Endereco</span>
                <span class="value"><?php echo htmlspecialchars($endereco_completo); ?></span>
            </div>
            <div class="review-row">
                <span class="label">Telefone</span>
                <span class="value"><?php echo htmlspecialchars($checkout['telefone']); ?></span>
            </div>
        </div>

        <div class="review-card">
            <h3><i class="fas fa-shopping-cart" style="color: var(--primary);"></i> Itens do Pedido</h3>
            <?php foreach ($itens as $item): ?>
            <div class="review-row">
                <span class="label"><?php echo $item['quantidade']; ?>x <?php echo htmlspecialchars($item['nome']); ?></span>
                <span class="value">Kz <?php echo number_format($item['subtotal'], 2, ',', '.'); ?></span>
            </div>
            <?php endforeach; ?>
            <div class="review-row" style="font-weight: 700; font-size: 1rem; border-top: 2px solid var(--border); padding-top: 0.75rem; margin-top: 0.5rem;">
                <span class="label">Total</span>
                <span class="value">Kz <?php echo number_format($total, 2, ',', '.'); ?></span>
            </div>
        </div>

        <form method="post" action="finalizar.php" onsubmit="this.querySelector('button[type=submit]').disabled=true; this.querySelector('button[type=submit]').innerHTML='Processando...';">
            <input type="hidden" name="etapa" value="confirmar">
            <input type="hidden" name="metodo_pagamento" value="<?php echo $checkout['metodo_pagamento']; ?>">
            <input type="hidden" name="endereco" value="<?php echo htmlspecialchars($endereco_completo); ?>">
            <input type="hidden" name="telefone" value="<?php echo htmlspecialchars($checkout['telefone']); ?>">

            <div style="display: flex; gap: 1rem; justify-content: space-between;">
                <a href="finalizar.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar e Editar</a>
                <button type="submit" class="btn btn-success" style="padding: 0.75rem 2rem;">
                    <i class="fas fa-check-circle"></i> Confirmar Pedido
                </button>
            </div>
        </form>

    <?php elseif ($etapa === 'confirmar'): ?>
        <!-- ETAPA 3: Processar -->
        <?php
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['etapa']) || $_POST['etapa'] !== 'confirmar') {
            header("Location: finalizar.php");
            exit();
        }

        $metodo_pg = $_POST['metodo_pagamento'] ?? '';
        $endereco = $_POST['endereco'] ?? '';
        $telefone = $_POST['telefone'] ?? '';

        $conn->begin_transaction();
        try {
            // Calcular total e verificar estoque
            $total = 0;
            $itens_processar = [];

            foreach ($_SESSION['carrinho'] as $id => $qtd) {
                $stmt = $conn->prepare("SELECT * FROM produtos WHERE id = ? FOR UPDATE");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $prod = $stmt->get_result()->fetch_assoc();

                if ($prod['estoque'] < $qtd) {
                    throw new Exception("Estoque insuficiente para: " . $prod['nome']);
                }

                $subtotal = $prod['preco'] * $qtd;
                $total += $subtotal;
                $itens_processar[] = ['id' => $id, 'qtd' => $qtd, 'preco' => $prod['preco']];
            }

            // Criar pedido com endereco e pagamento
            $stmt = $conn->prepare("INSERT INTO pedidos (usuario_id, total, endereco, telefone, metodo_pagamento) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("idsss", $usuario_id, $total, $endereco, $telefone, $metodo_pg);
            $stmt->execute();
            $pedido_id = $conn->insert_id;

            // Inserir itens e reduzir estoque
            foreach ($itens_processar as $item) {
                $stmt = $conn->prepare("INSERT INTO itens_pedido (pedido_id, produto_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("iiid", $pedido_id, $item['id'], $item['qtd'], $item['preco']);
                $stmt->execute();

                $stmt = $conn->prepare("UPDATE produtos SET estoque = estoque - ? WHERE id = ?");
                $stmt->bind_param("ii", $item['qtd'], $item['id']);
                $stmt->execute();
            }

            $conn->commit();
            unset($_SESSION['carrinho']);
            unset($_SESSION['checkout']);
            ?>
            <div style="text-align: center; padding: 2rem 0;">
                <div style="font-size: 4rem; margin-bottom: 1rem;">✅</div>
                <h2>Pedido Realizado com Sucesso!</h2>
                <p style="color: var(--text-light); margin-bottom: 0.5rem;">Obrigado por comprar conosco.</p>
                <p style="font-size: 1.1rem; margin-bottom: 0.5rem;">Pedido <strong>#<?php echo $pedido_id; ?></strong></p>
                <p style="font-size: 0.9rem; color: var(--text-light); margin-bottom: 1.5rem;">
                    <i class="fas fa-credit-card"></i> <?php echo $metodos_pagamento[$metodo_pg]['nome'] ?? $metodo_pg; ?> &mdash; 
                    <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($endereco); ?>
                </p>

                <div style="background: var(--card); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 1.5rem; max-width: 400px; margin: 0 auto 1.5rem;">
                    <h3 style="font-size: 0.95rem; margin-bottom: 0.75rem;">Dados para Transferencia</h3>
                    <?php if ($metodo_pg && isset($metodos_pagamento[$metodo_pg])): ?>
                    <div style="text-align: left; font-size: 0.9rem;">
                        <div style="display: flex; justify-content: space-between; padding: 0.3rem 0;">
                            <span style="color: var(--text-light);">Banco:</span>
                            <span style="font-weight: 600;"><?php echo $metodos_pagamento[$metodo_pg]['nome']; ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 0.3rem 0;">
                            <span style="color: var(--text-light);">Titular:</span>
                            <span style="font-weight: 600;"><?php echo $metodos_pagamento[$metodo_pg]['titular']; ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 0.3rem 0;">
                            <span style="color: var(--text-light);">Conta:</span>
                            <span style="font-weight: 600;"><?php echo $metodos_pagamento[$metodo_pg]['conta']; ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 0.3rem 0;">
                            <span style="color: var(--text-light);">Valor:</span>
                            <span style="font-weight: 700; color: var(--primary);">Kz <?php echo number_format($total, 2, ',', '.'); ?></span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <a href="pedidos.php" class="btn btn-primary"><i class="fas fa-receipt"></i> Ver Meus Pedidos</a>
                <a href="index.php" class="btn btn-secondary" style="margin-left: 0.5rem;">Continuar Comprando</a>
            </div>
            <?php
        } catch (Exception $e) {
            $conn->rollback();
            unset($_SESSION['checkout']);
            ?>
            <div style="text-align: center; padding: 2rem 0;">
                <div style="font-size: 4rem; margin-bottom: 1rem;">❌</div>
                <h2>Erro ao processar pedido</h2>
                <div class="alert alert-danger"><?php echo $e->getMessage(); ?></div>
                <a href="carrinho.php" class="btn btn-primary">Voltar ao Carrinho</a>
            </div>
            <?php
        }
        require_once 'footer.php';
        exit();
    endif; ?>
</div>

<?php echo $animais; ?>
<?php require_once 'footer.php'; ?>
