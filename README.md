# Lakastech - Loja Virtual Angolana

![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=flat&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.x-4479A1?style=flat&logo=mysql)
![XAMPP](https://img.shields.io/badge/XAMPP-8.x-FB7A24?style=flat&logo=xampp)
![License](https://img.shields.io/badge/license-MIT-green)

> **Lakastech** — Loja virtual angolana com tema glass macOS, animais africanos animados, dark mode e suporte a Multicaixa Express, BFA Net e BAI Directo.

---

## Funcionalidades

### Loja
- Catalogo de produtos com grid responsivo e categorias
- Carrinho de compras com actualizacao de quantidade
- **Checkout em 3 etapas**: Dados -> Revisao -> Confirmacao
- Endereco de entrega (rua, bairro, municipio, telefone)
- Selecao de metodo de pagamento (Multicaixa Express, BFA Net, BAI Directo)
- Dados bancarios exibidos apos confirmacao do pedido
- Transacao segura com ROLLBACK em caso de erro

### Utilizadores
- Registo e login com sessao
- Perfil com abas: Dados Pessoais + Pagamento
- Editar nome/email, alterar senha
- Gerir dados de pagamento pessoais (salvar/eliminar)
- Historico de pedidos em formato card
- Tema dark/light persistente (localStorage)

### Administracao
- **Produtos** (`admin.php`): CRUD completo, upload de imagem, busca, ordenacao, estoque inline, categorias com autocomplete, preview de imagem
- **Pedidos** (`admin_pedidos.php`): Lista em cards, busca por ID/nome/email, filtro por status (stats clicaveis), actualizacao de status inline
- **Utilizadores** (`admin_usuarios.php`): Lista com editar, mudar tipo, resetar senha, excluir, estatisticas de pedidos por usuario
- **Pagamentos** (`admin_pagamento.php`): Gerir metodos de pagamento da loja (nome, descricao, conta/IBAN, titular, activo/inactivo)

### Design
- **Glassmorphism macOS** — cards transparentes com backdrop-filter blur e saturação
- **Animais africanos animados** em SVG com CSS keyframes (elefante, leao, girafa, zebra, macaco, rinoceronte)
- **Split layout** no login/registo com mascote interativo
- **Icones flutuantes** e particulas decorativas
- Totalmente responsivo

---

## Animais Africanos

| Animal | Paginas | Animacao |
|--------|---------|----------|
| Elefante | Catalogo, Admin Pagamentos | Tromba e orelhas |
| Leao | Meus Pedidos, Admin Produtos | Juba tremendo |
| Girafa | Carrinho, Perfil, Admin Pagamentos | Pescoco |
| Zebra | Carrinho, Meus Pedidos | Galope |
| Macaco | Catalogo, Admin Utilizadores | Bracos |
| Rinoceronte | Admin Produtos | Corpo |

---

## Como Instalar

### Pre-requisitos
- XAMPP (PHP 8.x + MySQL)
- Git (opcional)

### Passos

```bash
# 1. Clonar
git clone https://github.com/seu-usuario/lakastech.git

# 2. Copiar para o XAMPP
cp -r lakastech C:/xampp/htdocs/

# 3. Iniciar Apache + MySQL no XAMPP

# 4. Setup do banco
http://localhost/loja_virtual/setup_db.php

# 5. Aceder a loja
http://localhost/loja_virtual/
```

### Utilizadores Padrao

| Tipo | Email | Senha |
|------|-------|-------|
| Administrador | admin@lakastech.com | admin123 |
| Cliente | cliente@gmail.com | 123456 |

---

## Estrutura do Projeto

```
loja_virtual/
├── admin.php                 # Gestao de produtos (CRUD, busca, ordenacao, estoque inline)
├── admin_pagamento.php       # Gestao de metodos de pagamento da loja
├── admin_pedidos.php         # Gestao de pedidos (cards, busca, filtro, status)
├── admin_usuarios.php        # Gestao de utilizadores (editar, tipo, reset senha)
├── adicionar_produtos.php    # Script para adicionar produtos
├── animais.php               # Funcoes PHP que retornam SVGs de animais africanos
├── atualizar_imagens.php     # Migrar imagens de URL para local
├── cadastro.php              # Registo de novo utilizador (split layout)
├── carrinho.php              # Carrinho de compras
├── config.php                # Conexao MySQL + sessao
├── criar_usuarios.php        # Criar utilizadores iniciais
├── database.sql              # Schema completo do banco
├── documentos/               # Documentacao extra
├── finalizar.php             # Checkout 3 etapas (dados -> revisar -> confirmar)
├── fix_passwords.php         # Reparar senhas
├── footer.php                # Footer + theme toggle JS
├── header.php                # Navbar + includes + admin dropdown
├── index.php                 # Catalogo de produtos
├── init.php                  # Inicializar utilizadores
├── login.php                 # Login (split layout com mascote)
├── logout.php                # Logout
├── pedidos.php               # Historico de pedidos do utilizador (cards)
├── perfil.php                # Perfil (abas: Dados Pessoais + Pagamento)
├── popular_produtos.php      # Popular tabela de produtos
├── reparar_db.php            # Reparar banco de dados
├── reset_senha.php           # Resetar senhas
├── security.php              # CSRF + validacao de upload
├── setup_db.php              # Setup inicial do banco
├── style.css                 # Estilos glass + animacoes
├── uploads/                  # Imagens dos produtos
└── README.md
```

---

## Banco de Dados

```sql
-- Tabelas:
--   usuarios          -> id, nome, email, senha, tipo, criado_em, pagamento_info
--   produtos          -> id, nome, descricao, preco, estoque, categoria, imagem, criado_em
--   pedidos           -> id, usuario_id, total, endereco, telefone, metodo_pagamento, status, data_pedido
--   itens_pedido      -> id, pedido_id, produto_id, quantidade, preco_unitario
--   metodos_pagamento -> id, nome, descricao, conta, titular, ativo, criado_em
```

---

## Personalizacao

### Cores
Edite as variaveis CSS em `style.css`:
```css
--primary: #0071e3;     /* Azul Apple */
--success: #34c759;     /* Verde */
--danger: #ff3b30;      /* Vermelho */
--accent: #f5a623;      /* Laranja */
```

### Animais
Cada animal e uma funcao PHP em `animais.php`:
```php
echo animal_elefante(80);  // Tamanho 80px
echo animal_leao(60);      // Tamanho 60px
```

---

## Seguranca

- Prepared statements (SQL injection)
- Senhas com `password_hash()` bcrypt
- Transacoes com ROLLBACK no checkout
- Upload validado (tamanho + MIME type)
- Sessao com verificacao de admin
- Output buffering (ob_start/ob_end_flush) para evitar headers already sent

---

## Screenshots

| Pagina | Descricao |
|--------|-----------|
| Catalogo | Grid de produtos com efeito glass e categorias |
| Login | Split com mascote animado + icones flutuantes |
| Carrinho | Tabela com quantidades e totais |
| Checkout | 3 etapas: dados, revisao, confirmacao |
| Admin Produtos | Cards com estoque inline, busca, ordenacao |
| Admin Pedidos | Cards com filtro, busca, status |
| Admin Utilizadores | Tabela com modal de edicao |
| Admin Pagamentos | Gerir contas bancarias da loja |
| Perfil | Abas Dados Pessoais + Pagamento com eliminar |

---

<p align="center">
  Feito com ♥ para Angola<br>
  <strong>Lakastech</strong> — Tecnologia ao seu alcance
</p>
