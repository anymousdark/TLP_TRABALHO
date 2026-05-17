CREATE DATABASE IF NOT EXISTS loja_virtual;
USE loja_virtual;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('cliente', 'admin') DEFAULT 'cliente',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    pagamento_info TEXT DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10, 2) NOT NULL,
    estoque INT NOT NULL DEFAULT 0,
    categoria VARCHAR(100) DEFAULT NULL,
    imagem VARCHAR(255),
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Garantir coluna estoque caso banco já exista com schema antigo
ALTER TABLE produtos ADD COLUMN IF NOT EXISTS estoque INT NOT NULL DEFAULT 0;


CREATE TABLE IF NOT EXISTS pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    endereco TEXT DEFAULT NULL,
    telefone VARCHAR(20) DEFAULT NULL,
    metodo_pagamento VARCHAR(50) DEFAULT NULL,
    status ENUM('pendente', 'pago', 'cancelado') DEFAULT 'pendente',
    data_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE IF NOT EXISTS itens_pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id)
);

CREATE TABLE IF NOT EXISTS metodos_pagamento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao VARCHAR(255) DEFAULT NULL,
    conta VARCHAR(100) NOT NULL,
    titular VARCHAR(100) NOT NULL,
    ativo TINYINT(1) DEFAULT 1,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO metodos_pagamento (nome, descricao, conta, titular) VALUES
('Multicaixa Express', 'Transferencia via Multicaixa Express', '933 078 978', 'Lakastech Lda'),
('BFA Net', 'Transferencia via BFA Net', 'AO06 0044 0000 1234 5678 9010 0', 'Lakastech Lda'),
('BAI Directo', 'Transferencia via BAI Directo', 'AO06 0050 0000 9876 5432 1010 0', 'Lakastech Lda');

-- Dados Iniciais (Admin: admin123, Cliente: 123456)
INSERT INTO usuarios (nome, email, senha, tipo) VALUES 
('Administrador Lakastech', 'admin@lakastech.com', '$2y$10$3wHsHvd9.6u8Ol5EZvJCJu.G8m8a2xqMvZ9rP5vK3kL2m9n8o7p6q', 'admin'),
('Cliente Teste', 'cliente@gmail.com', '$2y$10$8V1Qs.8V8V1Qs.8V8V1Qs.8V8V1Qs.8V8V1Qs.8V8V1Qs.8V8V1Qs.', 'cliente');

INSERT INTO produtos (nome, descricao, preco, estoque, imagem) VALUES 
('Smartphone Galaxy S23', 'Samsung Galaxy S23 256GB 5G', 4500.00, 10, 'uploads/smartphone.jpg'),
('Notebook Dell XPS 13', 'Notebook Dell XPS 13 Intel Core i7', 8900.00, 5, 'uploads/notebook.jpg'),
('Monitor LG UltraWide', 'Monitor LG 29 polegadas UltraWide', 1200.00, 8, 'uploads/monitor.jpg'),
('Teclado Mecanico RGB', 'Teclado Mecanico Switch Blue RGB', 350.00, 15, 'uploads/teclado.jpg'),
('Mouse Gamer Logi', 'Mouse Gamer Logitech G502 Hero', 280.00, 20, 'uploads/mouse.jpg');
