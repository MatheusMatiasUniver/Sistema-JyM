-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: localhost    Database: jym
-- ------------------------------------------------------
-- Server version	8.4.3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `academias`
--

DROP TABLE IF EXISTS `academias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academias` (
  `idAcademia` int unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `CNPJ` varchar(18) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `endereco` text COLLATE utf8mb4_unicode_ci,
  `responsavel` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`idAcademia`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `usuarioId` int unsigned DEFAULT NULL,
  `modulo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `acao` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entidade` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entidadeId` int unsigned DEFAULT NULL,
  `dados` json DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `activity_logs_usuarioid_index` (`usuarioId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ajustes_sistema`
--

DROP TABLE IF EXISTS `ajustes_sistema`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ajustes_sistema` (
  `idAjuste` int unsigned NOT NULL AUTO_INCREMENT,
  `idAcademia` int unsigned NOT NULL,
  `diaVencimentoSalarios` tinyint unsigned NOT NULL DEFAULT '5',
  `clienteOpcionalVenda` tinyint(1) NOT NULL DEFAULT '0',
  `formasPagamentoAceitas` json DEFAULT NULL,
  `permitirEdicaoManualEstoque` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idAjuste`),
  KEY `ajustes_sistema_idacademia_index` (`idAcademia`),
  CONSTRAINT `ajustes_sistema_idacademia_foreign` FOREIGN KEY (`idAcademia`) REFERENCES `academias` (`idAcademia`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorias` (
  `idCategoria` int unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` text COLLATE utf8mb4_unicode_ci,
  `status` enum('Ativo','Inativo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Ativo',
  `idAcademia` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`idCategoria`),
  UNIQUE KEY `categorias_nome_idacademia_unique` (`nome`,`idAcademia`),
  KEY `categorias_nome_index` (`nome`),
  KEY `categorias_status_index` (`status`),
  KEY `categorias_idacademia_index` (`idAcademia`),
  CONSTRAINT `categorias_idacademia_foreign` FOREIGN KEY (`idAcademia`) REFERENCES `academias` (`idAcademia`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `categorias_contas_pagar`
--

DROP TABLE IF EXISTS `categorias_contas_pagar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorias_contas_pagar` (
  `idCategoriaContaPagar` int unsigned NOT NULL AUTO_INCREMENT,
  `idAcademia` int unsigned NOT NULL,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ativa` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idCategoriaContaPagar`),
  KEY `categorias_contas_pagar_idacademia_index` (`idAcademia`),
  CONSTRAINT `categorias_contas_pagar_idacademia_foreign` FOREIGN KEY (`idAcademia`) REFERENCES `academias` (`idAcademia`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clientes` (
  `idCliente` int unsigned NOT NULL AUTO_INCREMENT,
  `idAcademia` int unsigned DEFAULT NULL,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cpf` varchar(11) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dataNascimento` date NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Ativo','Inativo','Inadimplente') COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigo_acesso` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `idUsuario` int unsigned DEFAULT NULL,
  `idPlano` int unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`idCliente`),
  UNIQUE KEY `clientes_cpf_unique` (`cpf`),
  KEY `clientes_idusuario_foreign` (`idUsuario`),
  KEY `clientes_idplano_foreign` (`idPlano`),
  KEY `clientes_idacademia_index` (`idAcademia`),
  CONSTRAINT `clientes_idacademia_foreign` FOREIGN KEY (`idAcademia`) REFERENCES `academias` (`idAcademia`) ON DELETE CASCADE,
  CONSTRAINT `clientes_idplano_foreign` FOREIGN KEY (`idPlano`) REFERENCES `plano_assinaturas` (`idPlano`) ON DELETE SET NULL,
  CONSTRAINT `clientes_idusuario_foreign` FOREIGN KEY (`idUsuario`) REFERENCES `users` (`idUsuario`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `compras`
--

DROP TABLE IF EXISTS `compras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `compras` (
  `idCompra` int unsigned NOT NULL AUTO_INCREMENT,
  `idAcademia` int unsigned NOT NULL,
  `idFornecedor` int unsigned NOT NULL,
  `dataEmissao` datetime NOT NULL,
  `status` enum('aberta','recebida','cancelada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aberta',
  `valorProdutos` decimal(10,2) NOT NULL DEFAULT '0.00',
  `valorFrete` decimal(10,2) NOT NULL DEFAULT '0.00',
  `valorDesconto` decimal(10,2) NOT NULL DEFAULT '0.00',
  `valorImpostos` decimal(10,2) NOT NULL DEFAULT '0.00',
  `valorTotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `observacoes` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`idCompra`),
  KEY `compras_idacademia_index` (`idAcademia`),
  KEY `compras_idfornecedor_index` (`idFornecedor`),
  CONSTRAINT `compras_idacademia_foreign` FOREIGN KEY (`idAcademia`) REFERENCES `academias` (`idAcademia`) ON DELETE CASCADE,
  CONSTRAINT `compras_idfornecedor_foreign` FOREIGN KEY (`idFornecedor`) REFERENCES `fornecedores` (`idFornecedor`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contas_pagar`
--

DROP TABLE IF EXISTS `contas_pagar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contas_pagar` (
  `idContaPagar` int unsigned NOT NULL AUTO_INCREMENT,
  `idAcademia` int unsigned NOT NULL,
  `idFornecedor` int unsigned DEFAULT NULL,
  `idFuncionario` int unsigned DEFAULT NULL,
  `idCategoriaContaPagar` int unsigned DEFAULT NULL,
  `documentoRef` int unsigned DEFAULT NULL,
  `descricao` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valorTotal` decimal(10,2) NOT NULL,
  `status` enum('aberta','paga','cancelada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aberta',
  `dataVencimento` date NOT NULL,
  `dataPagamento` date DEFAULT NULL,
  `formaPagamento` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`idContaPagar`),
  KEY `contas_pagar_idacademia_index` (`idAcademia`),
  KEY `contas_pagar_idfornecedor_index` (`idFornecedor`),
  KEY `contas_pagar_idcategoriacontapagar_foreign` (`idCategoriaContaPagar`),
  KEY `contas_pagar_idfuncionario_foreign` (`idFuncionario`),
  CONSTRAINT `contas_pagar_idacademia_foreign` FOREIGN KEY (`idAcademia`) REFERENCES `academias` (`idAcademia`) ON DELETE CASCADE,
  CONSTRAINT `contas_pagar_idcategoriacontapagar_foreign` FOREIGN KEY (`idCategoriaContaPagar`) REFERENCES `categorias_contas_pagar` (`idCategoriaContaPagar`) ON DELETE SET NULL,
  CONSTRAINT `contas_pagar_idfornecedor_foreign` FOREIGN KEY (`idFornecedor`) REFERENCES `fornecedores` (`idFornecedor`) ON DELETE RESTRICT,
  CONSTRAINT `contas_pagar_idfuncionario_foreign` FOREIGN KEY (`idFuncionario`) REFERENCES `users` (`idUsuario`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=112 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contas_receber`
--

DROP TABLE IF EXISTS `contas_receber`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contas_receber` (
  `idContaReceber` int unsigned NOT NULL AUTO_INCREMENT,
  `idAcademia` int unsigned NOT NULL,
  `idCliente` int unsigned DEFAULT NULL,
  `documentoRef` int unsigned DEFAULT NULL,
  `descricao` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valorTotal` decimal(10,2) NOT NULL,
  `status` enum('aberta','recebida','cancelada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aberta',
  `dataVencimento` date DEFAULT NULL,
  `dataRecebimento` date DEFAULT NULL,
  `formaRecebimento` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`idContaReceber`),
  KEY `contas_receber_idacademia_index` (`idAcademia`),
  KEY `contas_receber_idcliente_index` (`idCliente`),
  CONSTRAINT `contas_receber_idacademia_foreign` FOREIGN KEY (`idAcademia`) REFERENCES `academias` (`idAcademia`) ON DELETE CASCADE,
  CONSTRAINT `contas_receber_idcliente_foreign` FOREIGN KEY (`idCliente`) REFERENCES `clientes` (`idCliente`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=262 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `entradas`
--

DROP TABLE IF EXISTS `entradas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `entradas` (
  `idEntrada` int unsigned NOT NULL AUTO_INCREMENT,
  `idAcademia` int unsigned DEFAULT NULL,
  `idCliente` int unsigned DEFAULT NULL,
  `dataHora` datetime NOT NULL,
  `metodo` enum('Reconhecimento Facial','CPF/Senha','Manual') COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`idEntrada`),
  KEY `entradas_datahora_index` (`dataHora`),
  KEY `entradas_idacademia_index` (`idAcademia`),
  KEY `entradas_idcliente_foreign` (`idCliente`),
  CONSTRAINT `entradas_idacademia_foreign` FOREIGN KEY (`idAcademia`) REFERENCES `academias` (`idAcademia`) ON DELETE CASCADE,
  CONSTRAINT `entradas_idcliente_foreign` FOREIGN KEY (`idCliente`) REFERENCES `clientes` (`idCliente`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3237 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `equipamentos`
--

DROP TABLE IF EXISTS `equipamentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `equipamentos` (
  `idEquipamento` int unsigned NOT NULL AUTO_INCREMENT,
  `idAcademia` int unsigned NOT NULL,
  `descricao` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fabricante` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modelo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numeroSerie` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dataAquisicao` date DEFAULT NULL,
  `valorAquisicao` decimal(10,2) DEFAULT NULL,
  `garantiaFim` date DEFAULT NULL,
  `centroCusto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Ativo','Em Manuten├º├úo','Desativado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Ativo',
  PRIMARY KEY (`idEquipamento`),
  KEY `equipamentos_idacademia_index` (`idAcademia`),
  CONSTRAINT `equipamentos_idacademia_foreign` FOREIGN KEY (`idAcademia`) REFERENCES `academias` (`idAcademia`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `face_descriptors`
--

DROP TABLE IF EXISTS `face_descriptors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `face_descriptors` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cliente_id` int unsigned NOT NULL,
  `descriptor` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `face_descriptors_cliente_id_foreign` (`cliente_id`),
  CONSTRAINT `face_descriptors_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`idCliente`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fornecedores`
--

DROP TABLE IF EXISTS `fornecedores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fornecedores` (
  `idFornecedor` int unsigned NOT NULL AUTO_INCREMENT,
  `idAcademia` int unsigned DEFAULT NULL,
  `razaoSocial` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cnpjCpf` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `inscricaoEstadual` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contato` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `endereco` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `condicaoPagamentoPadrao` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idFornecedor`),
  KEY `fornecedores_idacademia_index` (`idAcademia`),
  CONSTRAINT `fornecedores_idacademia_foreign` FOREIGN KEY (`idAcademia`) REFERENCES `academias` (`idAcademia`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `itens_compras`
--

DROP TABLE IF EXISTS `itens_compras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `itens_compras` (
  `idItemCompra` int unsigned NOT NULL AUTO_INCREMENT,
  `idCompra` int unsigned NOT NULL,
  `idProduto` int unsigned NOT NULL,
  `quantidade` int NOT NULL,
  `precoUnitario` decimal(10,2) NOT NULL,
  `descontoPercent` decimal(5,2) DEFAULT NULL,
  `custoRateadoTotal` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`idItemCompra`),
  KEY `itens_compras_idcompra_index` (`idCompra`),
  KEY `itens_compras_idproduto_index` (`idProduto`),
  CONSTRAINT `itens_compras_idcompra_foreign` FOREIGN KEY (`idCompra`) REFERENCES `compras` (`idCompra`) ON DELETE CASCADE,
  CONSTRAINT `itens_compras_idproduto_foreign` FOREIGN KEY (`idProduto`) REFERENCES `produtos` (`idProduto`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=226 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `itens_vendas`
--

DROP TABLE IF EXISTS `itens_vendas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `itens_vendas` (
  `idItem` int unsigned NOT NULL AUTO_INCREMENT,
  `idVenda` int unsigned DEFAULT NULL,
  `idProduto` int unsigned DEFAULT NULL,
  `quantidade` int DEFAULT NULL,
  `precoUnitario` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`idItem`),
  KEY `itens_vendas_idvenda_foreign` (`idVenda`),
  KEY `itens_vendas_idproduto_foreign` (`idProduto`),
  CONSTRAINT `itens_vendas_idproduto_foreign` FOREIGN KEY (`idProduto`) REFERENCES `produtos` (`idProduto`) ON DELETE CASCADE,
  CONSTRAINT `itens_vendas_idvenda_foreign` FOREIGN KEY (`idVenda`) REFERENCES `venda_produtos` (`idVenda`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2673 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kiosk_status`
--

DROP TABLE IF EXISTS `kiosk_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kiosk_status` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `is_registering` tinyint(1) NOT NULL DEFAULT '0',
  `message` text COLLATE utf8mb4_unicode_ci,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `manutencoes_equipamento`
--

DROP TABLE IF EXISTS `manutencoes_equipamento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `manutencoes_equipamento` (
  `idManutencao` int unsigned NOT NULL AUTO_INCREMENT,
  `idEquipamento` int unsigned NOT NULL,
  `tipo` enum('preventiva','corretiva') COLLATE utf8mb4_unicode_ci NOT NULL,
  `dataSolicitacao` date NOT NULL,
  `dataProgramada` date DEFAULT NULL,
  `dataExecucao` date DEFAULT NULL,
  `custo` decimal(10,2) DEFAULT NULL,
  `responsavel` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Pendente','Conclu├¡da') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pendente',
  `fornecedorId` int unsigned DEFAULT NULL,
  `descricao` text COLLATE utf8mb4_unicode_ci,
  `servicoRealizado` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`idManutencao`),
  KEY `manutencoes_equipamento_idequipamento_index` (`idEquipamento`),
  KEY `manutencoes_equipamento_fornecedorid_foreign` (`fornecedorId`),
  CONSTRAINT `manutencoes_equipamento_fornecedorid_foreign` FOREIGN KEY (`fornecedorId`) REFERENCES `fornecedores` (`idFornecedor`) ON DELETE SET NULL,
  CONSTRAINT `manutencoes_equipamento_idequipamento_foreign` FOREIGN KEY (`idEquipamento`) REFERENCES `equipamentos` (`idEquipamento`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `marcas`
--

DROP TABLE IF EXISTS `marcas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `marcas` (
  `idMarca` int unsigned NOT NULL AUTO_INCREMENT,
  `idAcademia` int unsigned DEFAULT NULL,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `paisOrigem` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `site` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idMarca`),
  KEY `marcas_idacademia_index` (`idAcademia`),
  CONSTRAINT `marcas_idacademia_foreign` FOREIGN KEY (`idAcademia`) REFERENCES `academias` (`idAcademia`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `materiais`
--

DROP TABLE IF EXISTS `materiais`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `materiais` (
  `idMaterial` int unsigned NOT NULL AUTO_INCREMENT,
  `idAcademia` int unsigned NOT NULL,
  `descricao` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estoque` int NOT NULL DEFAULT '0',
  `unidadeMedida` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estoqueMinimo` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`idMaterial`),
  KEY `materiais_idacademia_index` (`idAcademia`),
  CONSTRAINT `materiais_idacademia_foreign` FOREIGN KEY (`idAcademia`) REFERENCES `academias` (`idAcademia`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mensalidades`
--

DROP TABLE IF EXISTS `mensalidades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mensalidades` (
  `idMensalidade` int unsigned NOT NULL AUTO_INCREMENT,
  `idCliente` int unsigned DEFAULT NULL,
  `idPlano` int unsigned NOT NULL,
  `idAcademia` int unsigned NOT NULL COMMENT 'Identificador da academia',
  `dataVencimento` date NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `status` enum('Paga','Pendente') COLLATE utf8mb4_unicode_ci NOT NULL,
  `dataPagamento` date DEFAULT NULL,
  `formaPagamento` enum('Dinheiro','PIX','Cart├úo de Cr├®dito','Cart├úo de D├®bito','Transfer├¬ncia Banc├íria','Boleto') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Forma de pagamento utilizada',
  PRIMARY KEY (`idMensalidade`),
  KEY `mensalidades_idplano_foreign` (`idPlano`),
  KEY `mensalidades_datavencimento_index` (`dataVencimento`),
  KEY `mensalidades_status_index` (`status`),
  KEY `mensalidades_datapagamento_index` (`dataPagamento`),
  KEY `mensalidades_idacademia_index` (`idAcademia`),
  KEY `mensalidades_idcliente_foreign` (`idCliente`),
  CONSTRAINT `mensalidades_idacademia_foreign` FOREIGN KEY (`idAcademia`) REFERENCES `academias` (`idAcademia`) ON DELETE CASCADE,
  CONSTRAINT `mensalidades_idcliente_foreign` FOREIGN KEY (`idCliente`) REFERENCES `clientes` (`idCliente`) ON DELETE SET NULL,
  CONSTRAINT `mensalidades_idplano_foreign` FOREIGN KEY (`idPlano`) REFERENCES `plano_assinaturas` (`idPlano`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=336 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `movimentacoes_estoque`
--

DROP TABLE IF EXISTS `movimentacoes_estoque`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `movimentacoes_estoque` (
  `idMovimentacao` int unsigned NOT NULL AUTO_INCREMENT,
  `idAcademia` int unsigned NOT NULL,
  `idProduto` int unsigned NOT NULL,
  `tipo` enum('entrada','saida','ajuste','devolucao','transferencia') COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantidade` int NOT NULL,
  `custoUnitario` decimal(10,2) DEFAULT NULL,
  `custoTotal` decimal(10,2) DEFAULT NULL,
  `origem` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referenciaId` int unsigned DEFAULT NULL,
  `motivo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dataMovimentacao` datetime NOT NULL,
  `usuarioId` int unsigned DEFAULT NULL,
  PRIMARY KEY (`idMovimentacao`),
  KEY `movimentacoes_estoque_idacademia_index` (`idAcademia`),
  KEY `movimentacoes_estoque_idproduto_index` (`idProduto`),
  CONSTRAINT `movimentacoes_estoque_idacademia_foreign` FOREIGN KEY (`idAcademia`) REFERENCES `academias` (`idAcademia`) ON DELETE CASCADE,
  CONSTRAINT `movimentacoes_estoque_idproduto_foreign` FOREIGN KEY (`idProduto`) REFERENCES `produtos` (`idProduto`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2898 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `movimentacoes_materiais`
--

DROP TABLE IF EXISTS `movimentacoes_materiais`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `movimentacoes_materiais` (
  `idMovMaterial` int unsigned NOT NULL AUTO_INCREMENT,
  `idAcademia` int unsigned NOT NULL,
  `idMaterial` int unsigned NOT NULL,
  `tipo` enum('entrada','saida','ajuste') COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantidade` int NOT NULL,
  `origem` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referenciaId` int unsigned DEFAULT NULL,
  `motivo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dataMovimentacao` datetime NOT NULL,
  `usuarioId` int unsigned DEFAULT NULL,
  PRIMARY KEY (`idMovMaterial`),
  KEY `movimentacoes_materiais_idacademia_index` (`idAcademia`),
  KEY `movimentacoes_materiais_idmaterial_index` (`idMaterial`),
  CONSTRAINT `movimentacoes_materiais_idacademia_foreign` FOREIGN KEY (`idAcademia`) REFERENCES `academias` (`idAcademia`) ON DELETE CASCADE,
  CONSTRAINT `movimentacoes_materiais_idmaterial_foreign` FOREIGN KEY (`idMaterial`) REFERENCES `materiais` (`idMaterial`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `plano_assinaturas`
--

DROP TABLE IF EXISTS `plano_assinaturas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `plano_assinaturas` (
  `idPlano` int unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descricao` text COLLATE utf8mb4_unicode_ci,
  `valor` decimal(10,2) DEFAULT NULL,
  `duracaoDias` int DEFAULT NULL,
  `idAcademia` int unsigned DEFAULT NULL,
  PRIMARY KEY (`idPlano`),
  KEY `plano_assinaturas_idacademia_foreign` (`idAcademia`),
  CONSTRAINT `plano_assinaturas_idacademia_foreign` FOREIGN KEY (`idAcademia`) REFERENCES `academias` (`idAcademia`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `produtos`
--

DROP TABLE IF EXISTS `produtos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `produtos` (
  `idProduto` int unsigned NOT NULL AUTO_INCREMENT,
  `idAcademia` int unsigned DEFAULT NULL,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `idCategoria` int unsigned DEFAULT NULL,
  `preco` decimal(10,2) DEFAULT NULL,
  `estoque` int DEFAULT NULL,
  `descricao` text COLLATE utf8mb4_unicode_ci,
  `imagem` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `precoCompra` decimal(10,2) DEFAULT NULL,
  `custoMedio` decimal(10,2) DEFAULT NULL,
  `estoqueMinimo` int NOT NULL DEFAULT '0',
  `idFornecedor` int unsigned DEFAULT NULL,
  `idMarca` int unsigned DEFAULT NULL,
  PRIMARY KEY (`idProduto`),
  KEY `produtos_idacademia_index` (`idAcademia`),
  KEY `produtos_idcategoria_index` (`idCategoria`),
  KEY `produtos_idfornecedor_index` (`idFornecedor`),
  KEY `produtos_idmarca_index` (`idMarca`),
  CONSTRAINT `produtos_idacademia_foreign` FOREIGN KEY (`idAcademia`) REFERENCES `academias` (`idAcademia`) ON DELETE CASCADE,
  CONSTRAINT `produtos_idcategoria_foreign` FOREIGN KEY (`idCategoria`) REFERENCES `categorias` (`idCategoria`) ON DELETE SET NULL,
  CONSTRAINT `produtos_idfornecedor_foreign` FOREIGN KEY (`idFornecedor`) REFERENCES `fornecedores` (`idFornecedor`) ON DELETE SET NULL,
  CONSTRAINT `produtos_idmarca_foreign` FOREIGN KEY (`idMarca`) REFERENCES `marcas` (`idMarca`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pulse_aggregates`
--

DROP TABLE IF EXISTS `pulse_aggregates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pulse_aggregates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `bucket` int unsigned NOT NULL,
  `period` mediumint unsigned NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `key` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `key_hash` binary(16) GENERATED ALWAYS AS (unhex(md5(`key`))) VIRTUAL,
  `aggregate` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` decimal(20,2) NOT NULL,
  `count` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pulse_aggregates_bucket_period_type_aggregate_key_hash_unique` (`bucket`,`period`,`type`,`aggregate`,`key_hash`),
  KEY `pulse_aggregates_period_bucket_index` (`period`,`bucket`),
  KEY `pulse_aggregates_type_index` (`type`),
  KEY `pulse_aggregates_period_type_aggregate_bucket_index` (`period`,`type`,`aggregate`,`bucket`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pulse_entries`
--

DROP TABLE IF EXISTS `pulse_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pulse_entries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` int unsigned NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `key` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `key_hash` binary(16) GENERATED ALWAYS AS (unhex(md5(`key`))) VIRTUAL,
  `value` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pulse_entries_timestamp_index` (`timestamp`),
  KEY `pulse_entries_type_index` (`type`),
  KEY `pulse_entries_key_hash_index` (`key_hash`),
  KEY `pulse_entries_timestamp_type_key_hash_value_index` (`timestamp`,`type`,`key_hash`,`value`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pulse_values`
--

DROP TABLE IF EXISTS `pulse_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pulse_values` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` int unsigned NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `key` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `key_hash` binary(16) GENERATED ALWAYS AS (unhex(md5(`key`))) VIRTUAL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pulse_values_type_key_hash_unique` (`type`,`key_hash`),
  KEY `pulse_values_timestamp_index` (`timestamp`),
  KEY `pulse_values_type_index` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `requisicoes_materiais`
--

DROP TABLE IF EXISTS `requisicoes_materiais`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `requisicoes_materiais` (
  `idRequisicao` int unsigned NOT NULL AUTO_INCREMENT,
  `idAcademia` int unsigned NOT NULL,
  `idMaterial` int unsigned NOT NULL,
  `quantidade` int NOT NULL,
  `centroCusto` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` datetime NOT NULL,
  `usuarioId` int unsigned DEFAULT NULL,
  `motivo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`idRequisicao`),
  KEY `requisicoes_materiais_idacademia_index` (`idAcademia`),
  KEY `requisicoes_materiais_idmaterial_index` (`idMaterial`),
  CONSTRAINT `requisicoes_materiais_idacademia_foreign` FOREIGN KEY (`idAcademia`) REFERENCES `academias` (`idAcademia`) ON DELETE CASCADE,
  CONSTRAINT `requisicoes_materiais_idmaterial_foreign` FOREIGN KEY (`idMaterial`) REFERENCES `materiais` (`idMaterial`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `idUsuario` int unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `usuario` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `senha` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nivelAcesso` enum('Administrador','Funcion├írio') COLLATE utf8mb4_unicode_ci NOT NULL,
  `idAcademia` int unsigned DEFAULT NULL COMMENT 'ID da academia ├á qual o funcion├írio est├í vinculado.',
  `salarioMensal` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`idUsuario`),
  UNIQUE KEY `users_usuario_unique` (`usuario`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_idacademia_index` (`idAcademia`),
  CONSTRAINT `users_idacademia_foreign` FOREIGN KEY (`idAcademia`) REFERENCES `academias` (`idAcademia`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usuario_academia`
--

DROP TABLE IF EXISTS `usuario_academia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuario_academia` (
  `idUsuario` int unsigned NOT NULL,
  `idAcademia` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`idUsuario`,`idAcademia`),
  KEY `usuario_academia_idacademia_foreign` (`idAcademia`),
  CONSTRAINT `usuario_academia_idacademia_foreign` FOREIGN KEY (`idAcademia`) REFERENCES `academias` (`idAcademia`) ON DELETE CASCADE,
  CONSTRAINT `usuario_academia_idusuario_foreign` FOREIGN KEY (`idUsuario`) REFERENCES `users` (`idUsuario`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `venda_produtos`
--

DROP TABLE IF EXISTS `venda_produtos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `venda_produtos` (
  `idVenda` int unsigned NOT NULL AUTO_INCREMENT,
  `idAcademia` int unsigned DEFAULT NULL,
  `idCliente` int unsigned DEFAULT NULL,
  `idUsuario` int unsigned DEFAULT NULL,
  `dataVenda` datetime DEFAULT NULL,
  `valorTotal` decimal(10,2) DEFAULT NULL,
  `formaPagamento` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`idVenda`),
  KEY `venda_produtos_idusuario_index` (`idUsuario`),
  KEY `venda_produtos_idacademia_index` (`idAcademia`),
  KEY `venda_produtos_idcliente_foreign` (`idCliente`),
  CONSTRAINT `venda_produtos_idacademia_foreign` FOREIGN KEY (`idAcademia`) REFERENCES `academias` (`idAcademia`) ON DELETE CASCADE,
  CONSTRAINT `venda_produtos_idcliente_foreign` FOREIGN KEY (`idCliente`) REFERENCES `clientes` (`idCliente`) ON DELETE SET NULL,
  CONSTRAINT `venda_produtos_idusuario_foreign` FOREIGN KEY (`idUsuario`) REFERENCES `users` (`idUsuario`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=1105 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-03 20:40:56
