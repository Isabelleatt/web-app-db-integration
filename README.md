# Gerenciador de Estoque - Aplicação Web com PHP e MySQL

Este repositório contém o código-fonte de uma aplicação web para gerenciamento de estoque de produtos, integrando-se a um banco de dados MySQL. O projeto foi desenvolvido com base no [tutorial da AWS](https://docs.aws.amazon.com/AmazonRDS/latest/UserGuide/TUT_WebAppWithRDS.html) que demonstra a integração de uma aplicação web com o Amazon RDS (_Relational Database Service_), utilizando PHP hospedado em uma instância EC2 da AWS.

## Funcionalidades

A aplicação oferece as seguintes funcionalidades:
1. **Adicionar Produtos**: Um formulário permite que novos produtos sejam cadastrados no banco de dados, com os seguintes campos:
   - Nome do Produto
   - Preço
   - Marca
   - Disponibilidade em Estoque (booleano)
   
2. **Listar Produtos**: A aplicação exibe todos os produtos cadastrados, com os seguintes detalhes:
   - ID
   - Nome do Produto
   - Preço
   - Marca
   - Status de Estoque (Disponível/Em falta)

## Estrutura do Banco de Dados

A aplicação utiliza uma tabela chamada `PRODUCTS` com os seguintes campos:

- **ID**: Chave primária, tipo INT, autoincrementada.
- **Product_Name**: Nome do produto, tipo VARCHAR(100).
- **Price**: Preço do produto, tipo DECIMAL(10, 2).
- **Brand**: Marca do produto, tipo VARCHAR(50).
- **In_Stock**: Status de estoque, tipo TINYINT(1), representando um valor booleano (1 para disponível, 0 para em falta).

### Tabela Adicional

Além da tabela `PRODUCTS`, foi criada uma segunda tabela, `ORDERS`, com os seguintes campos:
- **Order_ID**: Chave primária, tipo INT, autoincrementada.
- **Customer_Name**: Nome do cliente, tipo VARCHAR(100).
- **Product_ID**: ID do produto relacionado, tipo INT (chave estrangeira).
- **Quantity**: Quantidade solicitada, tipo INT.
- **Order_Date**: Data do pedido, tipo DATE.

Essa tabela foi adicionada conforme o enunciado do projeto, com pelo menos 4 campos e 3 tipos de dados diferentes (INT, VARCHAR, DATE).

## Configuração e Deploy

### Pré-requisitos

Certifique-se de ter os seguintes serviços configurados:

1. **Instância EC2 na AWS**:
   - Uma instância Amazon Linux ou Ubuntu, com Apache e PHP instalados.
   - Acesso à instância via SSH para gerenciar arquivos e o banco de dados.
   
2. **Banco de Dados MySQL (Amazon RDS ou MySQL local)**:
   - Um banco de dados MySQL configurado, acessível pela instância EC2.
   - As credenciais devem ser configuradas no arquivo `dbinfo.inc`.

### Passos para Configuração

1. **Clone o Repositório**:
   ```bash
   git clone https://github.com/Isabelleatt/web-app-db-integration.git
   ```

2. **Configuração do Banco de Dados**:
   No arquivo `inc/dbinfo.inc`, insira suas credenciais de conexão ao banco de dados:
   ```php
   define('DB_SERVER', 'endereco_do_servidor');
   define('DB_USERNAME', 'usuario');
   define('DB_PASSWORD', 'senha');
   define('DB_DATABASE', 'nome_do_banco');
   ```

3. **Criação das Tabelas**:
   O script PHP inicializa automaticamente a tabela `PRODUCTS` ao ser acessado pela primeira vez. Para criar a tabela adicional `ORDERS`, execute o script SQL fornecido no arquivo `create_orders_table.sql`.

4. **Deploy e Execução**:
   Acesse a URL pública da sua instância EC2 para interagir com a aplicação:
   ```
   http://<dns-publico-ec2>/SamplePage.php
   ```

## Infraestrutura AWS Utilizada

### Diagrama da Infraestrutura

O diagrama acima ilustra a comunicação entre os componentes da infraestrutura.

![Diagrama da Infraestrutura](https://docs.aws.amazon.com/images/AmazonRDS/latest/UserGuide/images/con-VPC-sec-grp.png)

O deploy desta aplicação utiliza os seguintes serviços da AWS:

1. **Amazon EC2**:
   - Instância EC2 executando **Amazon Linux 2023**, configurada com Apache e PHP.
   - A instância é responsável por servir o conteúdo da aplicação web e interagir com o banco de dados.

2. **Amazon RDS**:
   - Se preferido, o banco de dados pode ser gerenciado pelo **Amazon RDS** (MariaDB).
   - A instância EC2 se conecta ao banco de dados RDS utilizando o endpoint privado configurado no mesmo VPC.

### Configurações de Segurança

- **Grupos de Segurança**:
   - EC2: Permitir tráfego HTTP (porta 80) e SSH (porta 22).
   - RDS: Permitir tráfego MySQL (porta 3306) apenas da instância EC2.
   
- **VPC e Sub-redes**:
   - A instância EC2 está localizada em uma sub-rede pública.
   - O banco de dados RDS (se utilizado) reside em uma sub-rede privada para maior segurança.

## Vídeo Demonstrativo

Este [vídeo](https://link-do-video) demonstra a aplicação em execução, com uma visão detalhada das máquinas/serviços AWS utilizados. O vídeo cobre:
- **Deploy da Instância EC2**: Configuração do Apache, PHP, e código da aplicação.
- **Configuração da Instância RDS** (se aplicável): Criação do banco de dados MariaDB/MySQL.
- **Conexões entre EC2 e RDS**: Como as instâncias EC2 e RDS se comunicam dentro da VPC.

## Como Reproduzir o Projeto

### 1. Criar e Configurar a Instância EC2:
   - Crie uma instância EC2 usando **Amazon Linux 2023**.
   - Instale Apache e PHP com os seguintes comandos:
     ```bash
     sudo yum update -y
     sudo yum install -y httpd php php-mysqlnd
     sudo systemctl start httpd
     sudo systemctl enable httpd
     ```

### 2. Configurar o Banco de Dados MySQL:
   - Use **Amazon RDS** para criar uma instância de banco de dados MySQL.
   - Configure permissões para permitir que a instância EC2 acesse o RDS via grupos de segurança.

### 3. Deploy da Aplicação:
   - Faça o upload dos arquivos da aplicação para o diretório `/var/www/html` da instância EC2:
     ```bash
     scp -i <chave.pem> -r ./web-app-db-integration/* ec2-user@<ec2-public-dns>:/var/www/html/
     ```

### 4. Acessar a Aplicação:
   - Abra o navegador e acesse o DNS público da instância EC2 para interagir com a aplicação:
     ```
     http://<ec2-public-dns>/index.php
     ```

---

## Conclusão

Este projeto demonstra como integrar uma aplicação web com uma base de dados MySQL utilizando serviços da AWS como EC2 e RDS. A aplicação gerencia produtos em um estoque e inclui funcionalidades adicionais de pedidos (`ORDERS`). A estrutura da aplicação é flexível e escalável, permitindo que seja adaptada para outros tipos de operações de banco de dados.

