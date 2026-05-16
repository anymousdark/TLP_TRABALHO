CREATE DATABASE IF NOT EXISTS loja_virtual;
USE loja_virtual;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('cliente', 'admin') DEFAULT 'cliente',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10, 2) NOT NULL,
    estoque INT NOT NULL DEFAULT 0,
    imagem VARCHAR(255),
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Garantir coluna estoque caso banco já exista com schema antigo
ALTER TABLE produtos ADD COLUMN IF NOT EXISTS estoque INT NOT NULL DEFAULT 0;


CREATE TABLE IF NOT EXISTS pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
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

-- Dados Iniciais (Admin: admin123, Cliente: 123456)
INSERT INTO usuarios (nome, email, senha, tipo) VALUES 
('Administrador TechStore', 'admin@techstore.com', '$2y$10$3wHsHvd9.6u8Ol5EZvJCJu.G8m8a2xqMvZ9rP5vK3kL2m9n8o7p6q', 'admin'),
('Cliente Teste', 'cliente@gmail.com', '$2y$10$8V1Qs.8V8V1Qs.8V8V1Qs.8V8V1Qs.8V8V1Qs.8V8V1Qs.8V8V1Qs.', 'cliente');

INSERT INTO produtos (nome, descricao, preco, estoque, imagem) VALUES 
('Smartphone Galaxy S23', 'Samsung Galaxy S23 256GB 5G', 4500.00, 10, 'https://images.unsplash.com/photo-1678911820864-e2c567c655d7?auto=format&fit=crop&q=80&w=400'),
('Notebook Dell XPS 13', 'Notebook Dell XPS 13 Intel Core i7', 8900.00, 5, 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?auto=format&fit=crop&q=80&w=400'),
('Monitor LG UltraWide', 'Monitor LG 29 polegadas UltraWide', 1200.00, 8, 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?auto=format&fit=crop&q=80&w=400'),
('Teclado Mecânico RGB', 'Teclado Mecânico Switch Blue RGB', 350.00, 15, 'https://images.unsplash.com/photo-1511467687858-23d96c32e4ae?auto=format&fit=crop&q=80&w=400'),
('Mouse Gamer Logi', 'Mouse Gamer Logitech G502 Hero', 280.00, 20, 'https://images.unsplash.com/photo-1527698266440-12104e498b76?auto=format&fit=crop&q=80&w=400');
