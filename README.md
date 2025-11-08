# Banco de Alimentos - MVP

## 1. Visão Geral e Funcionalidades

Este projeto é um MVP (Produto Mínimo Viável) em **PHP e MySQL** para gerenciar transações e estoque de alimentos.

* **Acesso Seguro:** Login/Logoff com sessão PHP.
* **CRUD Completo:** Gerenciamento de Doadores, Instituições e Transações (Doações/Retiradas).
* **Controle de Estoque:** Atualização automática do saldo via lógica de Back-end.

***

## 2. Arquitetura Técnica e Objetos SQL

A aplicação utiliza objetos avançados do banco de dados para garantir a **integridade transacional** (Back-end) e a velocidade de relatórios (Front-end).

| Tipo | Nome | Finalidade |
| :--- | :--- | :--- |
| **Stored Procedure** | `sp_registrar_doacao` | Registra doação e incrementa o estoque (Transação). |
| **Trigger** | `trg_atualiza_estoque_edicao` | Corrige o saldo no estoque em caso de edição de quantidade ou tipo de alimento. |
| **View** | `vw_relatorio_doacoes` | Simplifica a consulta de histórico de doações. |
| **Tabela** | `controle_estoque` | Mantém o saldo atualizado de cada item. |

***

## 3. Setup e Instalação

### Pré-requisitos

Instalar **XAMPP** (ou similar) com Apache, PHP e MySQL.

### Instruções

1.  Clone o repositório para a pasta `htdocs` (Ex: `banco-de-alimentos-AEP`).
2.  Inicie **Apache** e **MySQL**.
3.  No phpMyAdmin ou MySQL Workbench, importe ou rode o arquivo **`bancoAlimentos.sql`** (cria tabelas, SPs e Triggers).
3.   Rode o script no banco **#SCRIPT PARA ZERAR TODOS OS ITENS E ESTOQUES DE TESTE##**, isso ira definir todos os itens para zero e assim garante que o calculo dos alimentos totais seja efetuado corretamente
4.  O arquivo `conexao.php` deve estar configurado para `usuario='root'` e `senha=''`.
5. Utilize a porta **3306** no MySQL dentro do XAMPP.

### Acesso

| Campo | Valor |
| :--- | :--- |
| **Usuário** | `admin` |
| **Senha** | `12345` |

***

## 4. Fluxo de Teste Crítico

1.  **Acesso Inicial:** Vá para `http://localhost/banco-de-alimentos-AEP/` (será redirecionado para o login).
2.  **Teste de Entrada (Soma):** Registre uma doação de 50 Kg de Arroz. O estoque em **Consultar Doações** deve mostrar **50.00 Kg**.
3.  **Teste de Exclusão (Subtração):** Delete o registro de 50 Kg. O estoque deve voltar para **0.00 Kg** (prova a reversão do saldo).
4.  **Teste de Edição:** Edite um registro de 10 Kg para 30 Kg. O estoque deve corrigir e somar **20 Kg** (a diferença).

***

## 5. Links úteis

* **LINK DO NOTION:** [Desenvolvimento do MVP - Banco de Alimentos](https://www.notion.so/Fase-de-Desenvolvimento-do-MVP-Banco-de-Alimentos-28e1b34d5f38801e927adcb58a0a9d6e?source=copy_link)

* **REPOSITÓRIO NO GITHUB:** [Banco de Alimentos AEP](https://github.com/guilhermetofoli/banco-de-alimentos-AEP.git)

* **Pitch no Youtube:** [MVP - Banco de Alimentos](https://youtu.be/3Ta8HYx-ZUk)