
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

#tabela estoque
CREATE TABLE IF NOT EXISTS controle_estoque (
    id_controle INT AUTO_INCREMENT PRIMARY KEY,
    fk_id_alimento INT UNIQUE,
    saldo_atual DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    FOREIGN KEY (fk_id_alimento) REFERENCES alimentos(id_alimento)
        ON DELETE CASCADE ON UPDATE CASCADE
);

INSERT INTO controle_estoque (fk_id_alimento, saldo_atual)
SELECT id_alimento, 0.00 FROM alimentos
ON DUPLICATE KEY UPDATE saldo_atual = saldo_atual;

#VIEW DE RELATORIO DE DOACOES#
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


#VIEW ESTOQUE GERAL#
CREATE VIEW vw_visao_estoque_geral AS
SELECT
    ali.nome_alimento AS Alimento,
    ce.saldo_atual AS Quantidade,
    ali.unidade_medida AS Unidade
FROM controle_estoque ce
JOIN alimentos ali ON ce.fk_id_alimento = ali.id_alimento
WHERE ce.saldo_atual > 0  -- Mostra apenas itens com saldo positivo
ORDER BY ali.nome_alimento ASC;


##SP de exclusão##a

-- Altera o delimitador
DELIMITER $$

-- SP para DELETAR uma doação e REVERTER o saldo no controle_estoque
CREATE PROCEDURE sp_deletar_doacao (
    IN p_id_doacao INT
)
BEGIN
    DECLARE v_quantidade DECIMAL(10, 2);
    DECLARE v_id_alimento INT;
    
    -- Inicia a transação
    START TRANSACTION;

    -- 1. Obtém a quantidade e o ID do alimento da doação a ser excluída
    SELECT quantidade, fk_id_alimento INTO v_quantidade, v_id_alimento
    FROM doacoes
    WHERE id_doacao = p_id_doacao FOR UPDATE; -- Bloqueia a linha

    -- 2. Deleta o registro de doação
    DELETE FROM doacoes WHERE id_doacao = p_id_doacao;

    -- 3. Atualiza o saldo do estoque (SUBTRAI a quantidade que estava no estoque)
    UPDATE controle_estoque
    SET saldo_atual = saldo_atual - v_quantidade
    WHERE fk_id_alimento = v_id_alimento;
    
    -- 4. Confirma as operações
    COMMIT;
END$$

-- Volta o delimitador padrão
DELIMITER ;


##VISAO GERAL ALIMENTOS
USE banco_de_alimentos;

DELIMITER $$

DROP PROCEDURE IF EXISTS sp_registrar_doacao;
CREATE PROCEDURE sp_registrar_doacao (
    IN p_id_doador INT,
    IN p_id_alimento INT,
    IN p_id_instituicao INT,
    IN p_quantidade DECIMAL(10, 2),
    IN p_observacoes TEXT
)
BEGIN
    START TRANSACTION;

    -- 1. Insere a doação (Isso está funcionando)
    INSERT INTO doacoes (
        fk_id_doador, fk_id_alimento, fk_id_instituicao, quantidade, observacoes
    ) VALUES (
        p_id_doador, p_id_alimento, p_id_instituicao, p_quantidade, p_observacoes
    );

    -- 2. Atualiza o saldo do estoque (REVISADO)
    -- Usa INSERT ... ON DUPLICATE KEY UPDATE para garantir que o registro exista, mesmo que não devesse.
    -- MAS vamos manter o UPDATE simples para verificar a lógica.
    
    UPDATE controle_estoque
    SET saldo_atual = saldo_atual + p_quantidade
    WHERE fk_id_alimento = p_id_alimento;
    
    COMMIT;
END$$

DELIMITER ;

#Trigger atualizar estoque
USE banco_de_alimentos;

DELIMITER $$

-- Remove o Trigger anterior (para recriar)
DROP TRIGGER IF EXISTS trg_atualiza_estoque_edicao;

CREATE TRIGGER trg_atualiza_estoque_edicao
BEFORE UPDATE ON doacoes
FOR EACH ROW
BEGIN
    DECLARE v_quantidade_antiga DECIMAL(10, 2);
    DECLARE v_quantidade_nova DECIMAL(10, 2);
    DECLARE v_diferenca DECIMAL(10, 2);

    SET v_quantidade_antiga = OLD.quantidade;
    SET v_quantidade_nova = NEW.quantidade;

    -- A) Se o ID do Alimento MUDOU (Ex: Arroz -> Feijão)
    IF OLD.fk_id_alimento <> NEW.fk_id_alimento THEN
        
        -- 1. Tira a quantidade ANTIGA do estoque ANTIGO (Arroz)
        UPDATE controle_estoque
        SET saldo_atual = saldo_atual - v_quantidade_antiga
        WHERE fk_id_alimento = OLD.fk_id_alimento;

        -- 2. Tenta somar a quantidade NOVA ao estoque NOVO (Leite Integral)
        -- Tenta atualizar o saldo
        UPDATE controle_estoque
        SET saldo_atual = saldo_atual + v_quantidade_nova
        WHERE fk_id_alimento = NEW.fk_id_alimento;
        
        -- 3. SE O UPDATE FALHAR (porque o Leite não tinha linha em controle_estoque), faz o INSERT
        IF ROW_COUNT() = 0 THEN
             INSERT INTO controle_estoque (fk_id_alimento, saldo_atual)
             VALUES (NEW.fk_id_alimento, v_quantidade_nova);
        END IF;


    -- B) Se o ID do Alimento NÃO MUDOU (Apenas a Quantidade mudou)
    ELSE 
        SET v_diferenca = v_quantidade_nova - v_quantidade_antiga;
        
        UPDATE controle_estoque
        SET saldo_atual = saldo_atual + v_diferenca
        WHERE fk_id_alimento = NEW.fk_id_alimento;
    END IF;
END$$

DELIMITER ;

#SCRIPT PARA ZERAR TODOS OS ITENS E ESTOQUES DE TESTE##

USE banco_de_alimentos;

-- 1. ZERA AS TABELAS TRANSACIONAIS (Filhas)
-- Remove todos os registros de doações e retiradas (Obrigatório devido às FKs)
TRUNCATE TABLE doacoes;
TRUNCATE TABLE retiradas;

-- 2. ZERA TODOS OS SALDOS DO ESTOQUE
-- Define o saldo_atual de todos os alimentos para 0.00
UPDATE controle_estoque
SET saldo_atual = 0.00;

-- OPCIONAL: Para garantir que nenhum Doador/Instituição de teste sobre
-- TRUNCATE TABLE doadores;
-- TRUNCATE TABLE instituicoes;

-- VERIFICAÇÃO FINAL
SELECT * FROM controle_estoque;