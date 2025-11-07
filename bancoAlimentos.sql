
#Criando o BD
CREATE DATABASE banco_de_alimentos;

#Seleciona o BD criado para utilizar
USE banco_de_alimentos;

#Criando tabela de Doadores (Pessoa Física e Jurídica)
CREATE TABLE IF NOT EXISTS doadores (
    id_doador INT AUTO_INCREMENT PRIMARY KEY,
    tipo_doador ENUM('Pessoa Física', 'Pessoa Jurídica') NOT NULL,
    nome_razao_social VARCHAR(255) NOT NULL,
    documento_cpf_cnpj VARCHAR(14) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE,
    telefone VARCHAR(15),
    endereco VARCHAR(255),
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

#Criando tabela com os alimentos para doação/serem doados.
CREATE TABLE IF NOT EXISTS alimentos (
    id_alimento INT AUTO_INCREMENT PRIMARY KEY,
    nome_alimento VARCHAR(100) NOT NULL UNIQUE,
    tipo ENUM('Não-Perecível', 'Perecível', 'Hortifrúti', 'Cesta Básica', 'Outros') NOT NULL,
    unidade_medida ENUM('Kg', 'Litro', 'Unidade', 'Cesta') NOT NULL
);

#Criando Tabela de para as instituições
CREATE TABLE IF NOT EXISTS instituicoes (
    id_instituicao INT AUTO_INCREMENT PRIMARY KEY,
    nome_fantasia VARCHAR(255) NOT NULL,
    cnpj VARCHAR(14) UNIQUE NOT NULL,
    responsavel_contato VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    telefone VARCHAR(15),
    endereco VARCHAR(255),
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


#Criando tabela de Doações
CREATE TABLE IF NOT EXISTS doacoes (
    id_doacao INT AUTO_INCREMENT PRIMARY KEY,
    
    -- Chaves Estrangeiras (FKs)
    fk_id_doador INT,
    fk_id_alimento INT,
    fk_id_instituicao INT,
    
    quantidade DECIMAL(10, 2) NOT NULL, 
    data_hora_doacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status_doacao ENUM('Recebida', 'Distribuída', 'Pendente') NOT NULL DEFAULT 'Recebida',
    observacoes TEXT,

    -- Definição das Chaves Estrangeiras (Relacionamentos)
    FOREIGN KEY (fk_id_doador) REFERENCES doadores(id_doador)
        ON DELETE RESTRICT ON UPDATE CASCADE,
        
    FOREIGN KEY (fk_id_alimento) REFERENCES alimentos(id_alimento)
        ON DELETE RESTRICT ON UPDATE CASCADE,
        
    FOREIGN KEY (fk_id_instituicao) REFERENCES instituicoes(id_instituicao)
        ON DELETE SET NULL ON UPDATE CASCADE -- Instituições podem ser removidas, mas o registro de doação permanece
);


#Inserção de Alimentos
INSERT INTO alimentos (nome_alimento, tipo, unidade_medida) VALUES 
('Arroz', 'Não-Perecível', 'Kg'),
('Feijão', 'Não-Perecível', 'Kg'),
('Banana', 'Hortifrúti', 'Kg'),
('Leite Integral', 'Perecível', 'Litro');

#Deletar Doador
SELECT * FROM doadores;
DELETE FROM doadores 
WHERE id_doador = 1;


#Deletar Instituição
SELECT * FROM instituicoes;
DELETE FROM instituicoes 
WHERE id_instituicao = 1;


#TABELA RETIRADAS

CREATE TABLE IF NOT EXISTS retiradas (
    id_retirada INT AUTO_INCREMENT PRIMARY KEY,
    fk_id_instituicao INT NOT NULL,
    fk_id_alimento INT NOT NULL,
    quantidade DECIMAL(10, 2) NOT NULL,
    observacoes TEXT,
    data_hora_retirada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (fk_id_instituicao) REFERENCES instituicoes(id_instituicao)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (fk_id_alimento) REFERENCES alimentos(id_alimento)
        ON DELETE RESTRICT ON UPDATE CASCADE
);


-- 1. Remove a VIEW antiga
DROP VIEW IF EXISTS vw_relatorio_doacoes;

-- 2. Cria a VIEW NOVAMENTE, incluindo o ID da doação
CREATE VIEW vw_relatorio_doacoes AS
SELECT
    d.id_doacao AS id_doacao, -- NOVO: Adiciona o ID da doação
    d.data_hora_doacao AS Data,
    doad.nome_razao_social AS Doador,
    inst.nome_fantasia AS Instituicao_Receptora,
    ali.nome_alimento AS Item,
    d.quantidade AS Quantidade,
    ali.unidade_medida AS Unidade
FROM doacoes d
JOIN doadores doad ON d.fk_id_doador = doad.id_doador
JOIN instituicoes inst ON d.fk_id_instituicao = inst.id_instituicao
JOIN alimentos ali ON d.fk_id_alimento = ali.id_alimento
ORDER BY d.data_hora_doacao DESC;