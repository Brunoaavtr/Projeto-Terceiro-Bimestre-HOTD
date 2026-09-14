-- MySQL dump 10.13  Distrib 8.0.19, for Win64 (x86_64)
--
-- Host: localhost    Database: projeto-terceiro-bimestre-hotd
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

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
-- Table structure for table `categoria`
--

DROP TABLE IF EXISTS `categoria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categoria` (
  `ID_CATEGORIA` int(11) NOT NULL AUTO_INCREMENT,
  `NM_CATEGORIA` varchar(100) NOT NULL,
  `DS_CATEGORIA` varchar(255) DEFAULT NULL,
  `FL_ATIVO` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`ID_CATEGORIA`),
  UNIQUE KEY `UK_CATEGORIA` (`NM_CATEGORIA`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categoria`
--

LOCK TABLES `categoria` WRITE;
/*!40000 ALTER TABLE `categoria` DISABLE KEYS */;
INSERT INTO `categoria` VALUES (1,'Funko Pop','Bonecos colecionáveis Funko Pop.',1),(2,'Action Figures','Figuras de ação e personagens colecionáveis.',1),(3,'Livros','Livros relacionados a fantasia, séries e universos de ficção.',1),(4,'Diversos','Outros produtos e itens do Covil do Dragão.',1);
/*!40000 ALTER TABLE `categoria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cidade`
--

DROP TABLE IF EXISTS `cidade`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cidade` (
  `ID_CIDADE` int(11) NOT NULL AUTO_INCREMENT,
  `NM_CIDADE` varchar(100) NOT NULL,
  `ID_ESTADO` int(11) NOT NULL,
  PRIMARY KEY (`ID_CIDADE`),
  KEY `ID_ESTADO` (`ID_ESTADO`),
  CONSTRAINT `cidade_fk_estado` FOREIGN KEY (`ID_ESTADO`) REFERENCES `estado` (`ID_ESTADO`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cidade`
--

LOCK TABLES `cidade` WRITE;
/*!40000 ALTER TABLE `cidade` DISABLE KEYS */;
INSERT INTO `cidade` VALUES (1,'Londrina',16),(2,'Maringá',16),(3,'Curitiba',16),(4,'Cascavel',16),(5,'Campo Mourão',16),(6,'São Paulo',25),(7,'Campinas',25),(8,'Santos',25),(9,'Rio de Janeiro',19),(10,'Belo Horizonte',13),(11,'Florianópolis',24);
/*!40000 ALTER TABLE `cidade` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `endereco`
--

DROP TABLE IF EXISTS `endereco`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `endereco` (
  `ID_ENDERECO` int(11) NOT NULL AUTO_INCREMENT,
  `ID_USUARIO` int(11) NOT NULL,
  `ID_CIDADE` int(11) NOT NULL,
  `DS_LOGRADOURO` varchar(150) NOT NULL,
  `NR_ENDERECO` varchar(20) NOT NULL,
  `DS_BAIRRO` varchar(100) NOT NULL,
  `DS_COMPLEMENTO` varchar(100) DEFAULT NULL,
  `NR_CEP` char(8) NOT NULL,
  PRIMARY KEY (`ID_ENDERECO`),
  KEY `ID_USUARIO` (`ID_USUARIO`),
  KEY `ID_CIDADE` (`ID_CIDADE`),
  CONSTRAINT `endereco_fk_cidade` FOREIGN KEY (`ID_CIDADE`) REFERENCES `cidade` (`ID_CIDADE`),
  CONSTRAINT `endereco_fk_usuario` FOREIGN KEY (`ID_USUARIO`) REFERENCES `usuario` (`ID_USUARIO`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `endereco`
--

LOCK TABLES `endereco` WRITE;
/*!40000 ALTER TABLE `endereco` DISABLE KEYS */;
INSERT INTO `endereco` VALUES (1,2,5,'Rua Akibono','753','lar parana','','87305130');
/*!40000 ALTER TABLE `endereco` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estado`
--

DROP TABLE IF EXISTS `estado`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estado` (
  `ID_ESTADO` int(11) NOT NULL AUTO_INCREMENT,
  `NM_ESTADO` varchar(50) NOT NULL,
  `SG_ESTADO` char(2) NOT NULL,
  PRIMARY KEY (`ID_ESTADO`),
  UNIQUE KEY `UK_ESTADO_SIGLA` (`SG_ESTADO`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estado`
--

LOCK TABLES `estado` WRITE;
/*!40000 ALTER TABLE `estado` DISABLE KEYS */;
INSERT INTO `estado` VALUES (1,'Acre','AC'),(2,'Alagoas','AL'),(3,'Amapá','AP'),(4,'Amazonas','AM'),(5,'Bahia','BA'),(6,'Ceará','CE'),(7,'Distrito Federal','DF'),(8,'Espírito Santo','ES'),(9,'Goiás','GO'),(10,'Maranhão','MA'),(11,'Mato Grosso','MT'),(12,'Mato Grosso do Sul','MS'),(13,'Minas Gerais','MG'),(14,'Pará','PA'),(15,'Paraíba','PB'),(16,'Paraná','PR'),(17,'Pernambuco','PE'),(18,'Piauí','PI'),(19,'Rio de Janeiro','RJ'),(20,'Rio Grande do Norte','RN'),(21,'Rio Grande do Sul','RS'),(22,'Rondônia','RO'),(23,'Roraima','RR'),(24,'Santa Catarina','SC'),(25,'São Paulo','SP'),(26,'Sergipe','SE'),(27,'Tocantins','TO');
/*!40000 ALTER TABLE `estado` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `marca`
--

DROP TABLE IF EXISTS `marca`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `marca` (
  `ID_MARCA` int(11) NOT NULL AUTO_INCREMENT,
  `NM_MARCA` varchar(100) NOT NULL,
  `FL_ATIVO` tinyint(1) NOT NULL DEFAULT 1,
  `DS_IMAGEM` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID_MARCA`),
  UNIQUE KEY `UK_MARCA` (`NM_MARCA`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `marca`
--

LOCK TABLES `marca` WRITE;
/*!40000 ALTER TABLE `marca` DISABLE KEYS */;
INSERT INTO `marca` VALUES (1,'Funko Pop',1,'IMG/marcas/marca_6aa5c36a91fa17.50885467.png'),(2,'Noble Collection',1,'IMG/marcas/marca_6aa5c883da48b9.74355681.jpg'),(3,'Le Dragon',1,'IMG/marcas/marca_6aa5c45c49ba91.80983724.png'),(4,'Random House Worlds',1,'IMG/marcas/marca_6aa5c93dc43910.63833150.jpg'),(5,'teste',0,NULL);
/*!40000 ALTER TABLE `marca` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pedido`
--

DROP TABLE IF EXISTS `pedido`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pedido` (
  `ID_PEDIDO` int(11) NOT NULL AUTO_INCREMENT,
  `ID_USUARIO` int(11) NOT NULL,
  `VL_PRODUTOS` decimal(10,2) NOT NULL,
  `VL_FRETE` decimal(10,2) NOT NULL DEFAULT 0.00,
  `VL_TOTAL` decimal(10,2) NOT NULL,
  `DS_CEP` varchar(8) NOT NULL,
  `DS_CIDADE` varchar(100) NOT NULL,
  `DS_UF` char(2) NOT NULL,
  `DS_STATUS` varchar(30) NOT NULL DEFAULT 'Pendente',
  `DT_PEDIDO` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ID_PEDIDO`),
  KEY `ID_USUARIO` (`ID_USUARIO`),
  CONSTRAINT `pedido_fk_usuario` FOREIGN KEY (`ID_USUARIO`) REFERENCES `usuario` (`ID_USUARIO`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedido`
--

LOCK TABLES `pedido` WRITE;
/*!40000 ALTER TABLE `pedido` DISABLE KEYS */;
INSERT INTO `pedido` VALUES (1,2,634.96,0.00,634.96,'87300000','Campo Mourão','PR','Concluído','2026-08-20 14:30:00'),(2,2,1374.10,0.00,1374.10,'87300000','Campo Mourão','PR','Concluído','2026-08-28 16:45:00'),(3,2,1083.63,0.00,1083.63,'87300000','Campo Mourão','PR','Concluído','2026-09-05 19:20:00');
/*!40000 ALTER TABLE `pedido` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pedido_item`
--

DROP TABLE IF EXISTS `pedido_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pedido_item` (
  `ID_PEDIDO_ITEM` int(11) NOT NULL AUTO_INCREMENT,
  `ID_PEDIDO` int(11) NOT NULL,
  `ID_PRODUTO` int(11) NOT NULL,
  `QT_PRODUTO` int(11) NOT NULL,
  `VL_UNITARIO` decimal(10,2) NOT NULL,
  `VL_SUBTOTAL` decimal(10,2) NOT NULL,
  PRIMARY KEY (`ID_PEDIDO_ITEM`),
  KEY `ID_PEDIDO` (`ID_PEDIDO`),
  KEY `ID_PRODUTO` (`ID_PRODUTO`),
  CONSTRAINT `pedido_item_fk_pedido` FOREIGN KEY (`ID_PEDIDO`) REFERENCES `pedido` (`ID_PEDIDO`),
  CONSTRAINT `pedido_item_fk_produto` FOREIGN KEY (`ID_PRODUTO`) REFERENCES `produto` (`ID_PRODUTO`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedido_item`
--

LOCK TABLES `pedido_item` WRITE;
/*!40000 ALTER TABLE `pedido_item` DISABLE KEYS */;
INSERT INTO `pedido_item` VALUES (1,1,3,2,254.98,509.96),(2,1,16,5,25.00,125.00),(3,2,4,3,199.90,599.70),(4,2,17,2,287.21,574.42),(5,2,5,1,199.98,199.98),(6,3,6,1,172.00,172.00),(7,3,16,2,25.00,50.00),(8,3,17,3,287.21,861.63);
/*!40000 ALTER TABLE `pedido_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `produto`
--

DROP TABLE IF EXISTS `produto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `produto` (
  `ID_PRODUTO` int(11) NOT NULL AUTO_INCREMENT,
  `NM_PRODUTO` varchar(150) NOT NULL,
  `DS_PRODUTO` varchar(500) DEFAULT NULL,
  `VL_PRODUTO` decimal(10,2) NOT NULL,
  `QT_ESTOQUE` int(11) NOT NULL DEFAULT 0,
  `FL_ATIVO` tinyint(1) NOT NULL DEFAULT 1,
  `ID_MARCA` int(11) NOT NULL,
  `ID_CATEGORIA` int(11) NOT NULL,
  PRIMARY KEY (`ID_PRODUTO`),
  KEY `ID_MARCA` (`ID_MARCA`),
  KEY `ID_CATEGORIA` (`ID_CATEGORIA`),
  CONSTRAINT `produto_fk_categoria` FOREIGN KEY (`ID_CATEGORIA`) REFERENCES `categoria` (`ID_CATEGORIA`),
  CONSTRAINT `produto_fk_marca` FOREIGN KEY (`ID_MARCA`) REFERENCES `marca` (`ID_MARCA`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produto`
--

LOCK TABLES `produto` WRITE;
/*!40000 ALTER TABLE `produto` DISABLE KEYS */;
INSERT INTO `produto` VALUES (1,'\"The Red Flame\" - Estatueta de Bebê Dragão','Cada peça é projetada como uma obra de arte colecionável, criada em colaboração com artistas 3D, para entusiastas e colecionadores de dragões.\r\n\r\nEstatueta exclusiva | Disponível apenas nesta loja\r\n\r\nO tempo de fabricação é de aproximadamente 2 a 3 meses.\r\n\r\nPose em pé: 20 x 27 x 14 cm\r\n\r\nPose sentada: 11 x 20 x 9 cm\r\n\r\nPose deitada: 15 x 14 x 7 cm',210.65,3,0,3,2),(2,'\"The Red Flame\" - Estatueta de Bebê Dragão','Cada peça é projetada como uma obra de arte colecionável, criada em colaboração com artistas 3D, para entusiastas e colecionadores de dragões.\r\n\r\nEstatueta exclusiva | Disponível apenas nesta loja\r\n\r\nO tempo de fabricação é de aproximadamente 2 a 3 meses.\r\n\r\nPosição em pé: 20 x 27 x 14 cm\r\n\r\nPosição sentada: 11 x 20 x 9 cm\r\n\r\nPosição deitada: 15 x 14 x 7 cm',203.67,5,0,3,2),(3,'\"A Chama Vermelha\" - Estatueta de Bebê Dragão','Cada peça é projetada como uma obra de arte colecionável, criada em colaboração com artistas 3D, para entusiastas e colecionadores de dragões.\r\n\r\nEstatueta exclusiva | Disponível apenas nesta loja\r\n\r\nO tempo de fabricação é de aproximadamente 2 a 3 meses.\r\n\r\nPosição em pé: 20 x 27 x 14 cm\r\n\r\nPosição sentada: 11 x 20 x 9 cm\r\n\r\nPosição deitada: 15 x 14 x 7 cm',254.98,3,1,3,2),(4,'\"Sopro Dourado\" - Estatueta de Bebê Dragão','Cada peça é projetada como uma obra de arte colecionável, criada em colaboração com artistas 3D, para entusiastas e colecionadores de dragões.\r\n\r\nEstatueta exclusiva | Disponível apenas nesta loja\r\n\r\nO tempo de fabricação é de aproximadamente 2 a 3 meses.\r\n\r\nPosição em pé: 20 x 27 x 14 cm\r\n\r\nPosição sentada: 11 x 20 x 9 cm\r\n\r\nPosição deitada: 15 x 14 x 7 cm',199.90,8,1,3,2),(5,'\"A Presa de Esmeralda\" - Estatueta de Bebê Dragão','Cada peça é projetada como uma obra de arte colecionável, criada em colaboração com artistas 3D, para entusiastas e colecionadores de dragões.\r\n\r\nEstatueta exclusiva | Disponível apenas nesta loja\r\n\r\nO tempo de fabricação é de aproximadamente 2 a 3 meses.\r\n\r\nPosição em pé: 20 x 27 x 14 cm\r\n\r\nPosição sentada: 11 x 20 x 9 cm\r\n\r\nPosição deitada: 15 x 14 x 7 cm',199.98,5,1,3,2),(6,'\"O Wyrm de Sangue\" - Estatueta de Embrião de Dragão','Cada peça é projetada como uma obra de arte colecionável, criada em colaboração com artistas 3D, para entusiastas e colecionadores de dragões.\r\n\r\nEstatueta exclusiva | Disponível apenas em nossa loja\r\n\r\nO tempo de produção é de aproximadamente 2 a 3 meses.\r\n\r\nDimensões: 12 x 12 cm | Embrião: 8 cm',172.00,2,1,3,2),(7,'\"A Rainha Vermelha\" - Estatueta de Embrião de Dragão','Cada peça é projetada como uma obra de arte colecionável, criada em colaboração com artistas 3D, para entusiastas e colecionadores de dragões.\r\n\r\nEstatueta exclusiva | Disponível apenas em nossa loja',172.12,4,1,3,2),(8,'\"A Fúria Verde\" - Estatueta de Dragão','Cada peça é projetada como uma obra de arte colecionável, criada em colaboração com artistas 3D, para entusiastas e colecionadores de dragões.\r\n\r\nEstatueta exclusiva | Inspirada no Dragão Vermax',374.98,2,1,3,2),(9,'\"O Dourado\" - Estatueta de Dragão','Cada peça é projetada como uma obra de arte colecionável, criada em colaboração com artistas 3D, para entusiastas e colecionadores de dragões.\r\n\r\nEstatueta exclusiva',476.43,2,1,3,2),(10,'\"O Rouba-Ovelhas\" - Estatueta de Dragão','Cada peça é projetada como uma obra de arte colecionável, criada em colaboração com artistas 3D, para entusiastas e colecionadores de dragões.\r\n\r\nEstatueta exclusiva | Disponível apenas nesta loja',387.32,1,1,3,2),(11,'\"A Dançarina da Lua\" - Estatueta de Dragão','Cada peça é projetada como uma obra de arte colecionável, criada em colaboração com artistas 3D, para entusiastas e colecionadores de dragões.\r\n\r\nEstatueta exclusiva',654.21,1,1,3,2),(12,'\"A Fúria de Bronze\" - Estatueta de Dragão','Cada peça é projetada como uma obra de arte colecionável, criada em colaboração com artistas 3D, para entusiastas e colecionadores de dragões.\r\n\r\nEstatueta exclusiva\r\n\r\nDimensões: 30 x 22 x 22 cm\r\n\r\nTempo de processamento: 2 a 3 meses',567.89,1,1,3,2),(13,'\"A Asa de Prata\" - Estatueta de Dragão','Cada peça é projetada como uma obra de arte colecionável, criada em colaboração com artistas 3D, para entusiastas e colecionadores de dragões.\r\n\r\nEstatueta exclusiva\r\n\r\nDimensões: 30 x 22 x 21 cm\r\n\r\nTempo de processamento: 2 a 3 meses',268.98,3,1,3,2),(14,'\"A Rainha Vermelha\" - Estatueta de Dragão','\"A Rainha Vermelha\" - Estatueta de Dragão\r\n\r\nCada peça é projetada como uma obra de arte colecionável, criada em colaboração com artistas 3D, para entusiastas e colecionadores de dragões.\r\n\r\nEstatueta exclusiva\r\n\r\nDimensões: 30 x 27 x 45 cm\r\n\r\nTempo de processamento: 2 a 3 meses',534.65,2,1,3,2),(15,'\"O Despertar dos Dragões\" Os Ovos Petrificados de Daenerys Targaryen','Inspirado nas relíquias históricas de A Guerra dos Tronos, este conjunto exclusivo reúne os três lendários ovos de dragão presenteados a Daenerys Targaryen em seu casamento. Cada peça é cuidadosamente esculpida para retratar a textura detalhada das escamas petrificadas e os tons distintos que deram origem a Drogon, Rhaegal e Viserion: o tom negro com detalhes em escarlate, o verde profundo com nuances douradas e o creme suave com brilho cintilante.',645.00,9,1,2,2),(16,'Boneco Funko Pop! Alicent Hightower','Adicione um toque da história de Westeros à sua coleção com este Funko Pop! exclusivo de Alicent Hightower, personagem central da aclamada série da HBO, House of the Dragon. Esta edição limitada, lançada na San Diego Comic-Con (SDCC) de 2022, captura a rainha em seu emblemático vestido verde, simbolizando a facção \"Os Verdes\" durante a lendária Dança dos Dragões.',25.00,100,1,1,1),(17,'Boneco Funko Pop! Rides Deluxe: Aegon Targaryen com Sunfyre','Reivindique o Trono de Ferro com o imperioso Aegon II Targaryen montado em Sunfyre (Girassol), o mais belo dragão de Westeros! Diretamente da aclamada série da HBO, House of the Dragon, esta peça da linha Pop! Rides Deluxe (#135) retrata o jovem rei vestindo sua armadura de batalha e a icônica coroa de Aegon, o Conquistador.',287.21,9,1,1,1);
/*!40000 ALTER TABLE `produto` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_produto_valores_positivos

BEFORE UPDATE ON produto

FOR EACH ROW

BEGIN

    IF NEW.VL_PRODUTO < 0 THEN

        SET NEW.VL_PRODUTO = ABS(NEW.VL_PRODUTO);

    END IF;



    IF NEW.QT_ESTOQUE < 0 THEN

        SET NEW.QT_ESTOQUE = ABS(NEW.QT_ESTOQUE);

    END IF;

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `produto_imagem`
--

DROP TABLE IF EXISTS `produto_imagem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `produto_imagem` (
  `ID_PRODUTO_IMAGEM` int(11) NOT NULL AUTO_INCREMENT,
  `ID_PRODUTO` int(11) NOT NULL,
  `DS_IMAGEM` varchar(255) NOT NULL,
  `FL_PRINCIPAL` tinyint(1) NOT NULL DEFAULT 0,
  `NR_ORDEM` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`ID_PRODUTO_IMAGEM`),
  KEY `ID_PRODUTO` (`ID_PRODUTO`),
  CONSTRAINT `produto_imagem_fk_produto` FOREIGN KEY (`ID_PRODUTO`) REFERENCES `produto` (`ID_PRODUTO`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produto_imagem`
--

LOCK TABLES `produto_imagem` WRITE;
/*!40000 ALTER TABLE `produto_imagem` DISABLE KEYS */;
INSERT INTO `produto_imagem` VALUES (2,1,'IMG/produtos/produto_1_6aa6001b624de2.55480926.webp',1,1),(3,2,'IMG/produtos/produto_2_6aa604d4e004b8.74414132.webp',1,1),(7,3,'IMG/produtos/produto_3_6aa605e59775e6.59133375.webp',1,1),(8,4,'IMG/produtos/produto_4_6aa606696769d6.28571794.webp',1,1),(9,5,'IMG/produtos/produto_5_6aa606f3116ca2.48441092.webp',1,1),(10,6,'IMG/produtos/produto_6_6aa60780be2473.30133455.webp',1,1),(11,7,'IMG/produtos/produto_7_6aa608206bf805.62414492.webp',1,1),(12,8,'IMG/produtos/produto_8_6aa608adc0a6a8.30220505.webp',1,1),(13,9,'IMG/produtos/produto_9_6aa6093dda4ec3.41108422.webp',1,1),(14,10,'IMG/produtos/produto_10_6aa609b4714118.36970839.webp',1,1),(15,11,'IMG/produtos/produto_11_6aa60a2e55bb06.17158845.webp',1,1),(16,12,'IMG/produtos/produto_12_6aa60ac6247cc9.77631743.webp',1,1),(17,13,'IMG/produtos/produto_13_6aa60b37a20bf7.13987836.webp',1,1),(18,14,'IMG/produtos/produto_14_6aa60bb02579c4.10954748.webp',1,1),(19,15,'IMG/produtos/produto_15_6aa60dd442c4a3.66495922.webp',1,1),(20,16,'IMG/produtos/produto_16_6aa60fabc81bf1.80794317.jpg',1,1),(21,17,'IMG/produtos/produto_17_6aa610075281e8.15329242.webp',1,1);
/*!40000 ALTER TABLE `produto_imagem` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `telefone`
--

DROP TABLE IF EXISTS `telefone`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `telefone` (
  `ID_TELEFONE` int(11) NOT NULL AUTO_INCREMENT,
  `ID_USUARIO` int(11) NOT NULL,
  `NR_TELEFONE` varchar(15) NOT NULL,
  PRIMARY KEY (`ID_TELEFONE`),
  KEY `ID_USUARIO` (`ID_USUARIO`),
  CONSTRAINT `telefone_fk_usuario` FOREIGN KEY (`ID_USUARIO`) REFERENCES `usuario` (`ID_USUARIO`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `telefone`
--

LOCK TABLES `telefone` WRITE;
/*!40000 ALTER TABLE `telefone` DISABLE KEYS */;
INSERT INTO `telefone` VALUES (1,2,'44988016468');
/*!40000 ALTER TABLE `telefone` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_usuario`
--

DROP TABLE IF EXISTS `tipo_usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipo_usuario` (
  `ID_TIPO_USUARIO` int(11) NOT NULL AUTO_INCREMENT,
  `NM_TIPO_USUARIO` varchar(20) NOT NULL,
  PRIMARY KEY (`ID_TIPO_USUARIO`),
  UNIQUE KEY `UK_TIPO_USUARIO` (`NM_TIPO_USUARIO`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_usuario`
--

LOCK TABLES `tipo_usuario` WRITE;
/*!40000 ALTER TABLE `tipo_usuario` DISABLE KEYS */;
INSERT INTO `tipo_usuario` VALUES (2,'admin'),(1,'usuario');
/*!40000 ALTER TABLE `tipo_usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuario` (
  `ID_USUARIO` int(11) NOT NULL AUTO_INCREMENT,
  `NM_USUARIO` varchar(100) NOT NULL,
  `DS_EMAIL` varchar(150) NOT NULL,
  `DS_SENHA` varchar(255) NOT NULL,
  `NR_CPF` char(11) NOT NULL,
  `DT_NASCIMENTO` date NOT NULL,
  `FL_ATIVO` tinyint(1) NOT NULL DEFAULT 1,
  `ID_TIPO_USUARIO` int(11) NOT NULL,
  `DS_FOTO` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID_USUARIO`),
  UNIQUE KEY `UK_USUARIO_EMAIL` (`DS_EMAIL`),
  UNIQUE KEY `UK_USUARIO_CPF` (`NR_CPF`),
  KEY `ID_TIPO_USUARIO` (`ID_TIPO_USUARIO`),
  CONSTRAINT `usuario_fk_tipo` FOREIGN KEY (`ID_TIPO_USUARIO`) REFERENCES `tipo_usuario` (`ID_TIPO_USUARIO`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` VALUES (1,'Administrador','admin@email.com','$2y$10$p8rFTHhfvZon7w1PFD4hIOZHmee9rN4VLr3qX1ySK0PoCMWsDW/LG','00000000000','2000-01-01',1,2,'IMG/usuarios/usuario_1_1789240946.jpg'),(2,'Bruno afonso de araújo viter','brunoaavtr@gmail.com','$2y$10$6hP/TCSYCa0ZJ2vrwfqt3e/HLFdzobHpsyQuMeICYawfIP3/j9F.e','13154067990','2003-11-28',1,1,NULL);
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `tipo` varchar(20) NOT NULL DEFAULT 'usuario',
  `datanascimento` date NOT NULL,
  `nome` varchar(100) NOT NULL DEFAULT 'Administrador',
  `cpf` varchar(14) NOT NULL DEFAULT '000.000.000-00',
  `ativo` varchar(3) NOT NULL DEFAULT 'Sim',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'admin@email.com','$2y$10$2fx3/PtE/1rjAjcaM9j1qOgecJmrd4LJgeUnbdn5pcI8LePFm0Pm.','admin','2000-01-01','Administrador','000.000.000-00','Sim'),(2,'brunoaavtr@gmail.com','$2y$10$Bpg2ZvuCK34fqSyZguPqh..BwXCPTTHKeqjjh4M1583bl37ToGcjC','usuario','2000-07-10','bruno teste','52998224725','Sim');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `vw_dashboard_pedidos`
--

DROP TABLE IF EXISTS `vw_dashboard_pedidos`;
/*!50001 DROP VIEW IF EXISTS `vw_dashboard_pedidos`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_dashboard_pedidos` AS SELECT 
 1 AS `ID_PEDIDO`,
 1 AS `ID_USUARIO`,
 1 AS `NM_USUARIO`,
 1 AS `DS_EMAIL`,
 1 AS `DS_STATUS`,
 1 AS `VL_PRODUTOS`,
 1 AS `VL_FRETE`,
 1 AS `VL_TOTAL`,
 1 AS `DT_PEDIDO`,
 1 AS `DS_CIDADE`,
 1 AS `DS_UF`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_dashboard_vendas_detalhadas`
--

DROP TABLE IF EXISTS `vw_dashboard_vendas_detalhadas`;
/*!50001 DROP VIEW IF EXISTS `vw_dashboard_vendas_detalhadas`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_dashboard_vendas_detalhadas` AS SELECT 
 1 AS `ID_PEDIDO`,
 1 AS `DT_PEDIDO`,
 1 AS `ID_PRODUTO`,
 1 AS `NM_PRODUTO`,
 1 AS `NM_MARCA`,
 1 AS `ID_CATEGORIA`,
 1 AS `NM_CATEGORIA`,
 1 AS `QT_PRODUTO`,
 1 AS `VL_UNITARIO`,
 1 AS `VL_SUBTOTAL`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_vendas_produtos`
--

DROP TABLE IF EXISTS `vw_vendas_produtos`;
/*!50001 DROP VIEW IF EXISTS `vw_vendas_produtos`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_vendas_produtos` AS SELECT 
 1 AS `ID_PRODUTO`,
 1 AS `NM_PRODUTO`,
 1 AS `NM_MARCA`,
 1 AS `ID_CATEGORIA`,
 1 AS `NM_CATEGORIA`,
 1 AS `QT_VENDIDA`,
 1 AS `VL_FATURADO`*/;
SET character_set_client = @saved_cs_client;

--
-- Dumping routines for database 'projeto-terceiro-bimestre-hotd'
--
/*!50003 DROP FUNCTION IF EXISTS `fn_calcular_subtotal` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_calcular_subtotal`(quantidade INT,

    valor_unitario DECIMAL(10,2)

) RETURNS decimal(10,2)
    DETERMINISTIC
RETURN quantidade * valor_unitario ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_dashboard_produtos` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_dashboard_produtos`(

    IN p_pagina INT,

    IN p_por_pagina INT,

    IN p_id_categoria INT

)
BEGIN

    DECLARE v_offset INT DEFAULT 0;



    IF p_pagina < 1 THEN

        SET p_pagina = 1;

    END IF;



    IF p_por_pagina < 1 THEN

        SET p_por_pagina = 10;

    END IF;



    SET v_offset = (p_pagina - 1) * p_por_pagina;



    SELECT

        ID_PRODUTO,

        NM_PRODUTO,

        NM_MARCA,

        ID_CATEGORIA,

        NM_CATEGORIA,

        QT_VENDIDA,

        VL_FATURADO

    FROM vw_vendas_produtos

    WHERE p_id_categoria IS NULL

       OR ID_CATEGORIA = p_id_categoria

    ORDER BY VL_FATURADO DESC

    LIMIT v_offset, p_por_pagina;

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Final view structure for view `vw_dashboard_pedidos`
--

/*!50001 DROP VIEW IF EXISTS `vw_dashboard_pedidos`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_dashboard_pedidos` AS select `pe`.`ID_PEDIDO` AS `ID_PEDIDO`,`pe`.`ID_USUARIO` AS `ID_USUARIO`,`u`.`NM_USUARIO` AS `NM_USUARIO`,`u`.`DS_EMAIL` AS `DS_EMAIL`,`pe`.`DS_STATUS` AS `DS_STATUS`,`pe`.`VL_PRODUTOS` AS `VL_PRODUTOS`,`pe`.`VL_FRETE` AS `VL_FRETE`,`pe`.`VL_TOTAL` AS `VL_TOTAL`,`pe`.`DT_PEDIDO` AS `DT_PEDIDO`,`pe`.`DS_CIDADE` AS `DS_CIDADE`,`pe`.`DS_UF` AS `DS_UF` from (`pedido` `pe` join `usuario` `u` on(`u`.`ID_USUARIO` = `pe`.`ID_USUARIO`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_dashboard_vendas_detalhadas`
--

/*!50001 DROP VIEW IF EXISTS `vw_dashboard_vendas_detalhadas`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_dashboard_vendas_detalhadas` AS select `pe`.`ID_PEDIDO` AS `ID_PEDIDO`,`pe`.`DT_PEDIDO` AS `DT_PEDIDO`,`p`.`ID_PRODUTO` AS `ID_PRODUTO`,`p`.`NM_PRODUTO` AS `NM_PRODUTO`,`m`.`NM_MARCA` AS `NM_MARCA`,`c`.`ID_CATEGORIA` AS `ID_CATEGORIA`,`c`.`NM_CATEGORIA` AS `NM_CATEGORIA`,`pi`.`QT_PRODUTO` AS `QT_PRODUTO`,`pi`.`VL_UNITARIO` AS `VL_UNITARIO`,`fn_calcular_subtotal`(`pi`.`QT_PRODUTO`,`pi`.`VL_UNITARIO`) AS `VL_SUBTOTAL` from ((((`pedido_item` `pi` join `pedido` `pe` on(`pe`.`ID_PEDIDO` = `pi`.`ID_PEDIDO`)) join `produto` `p` on(`p`.`ID_PRODUTO` = `pi`.`ID_PRODUTO`)) join `marca` `m` on(`m`.`ID_MARCA` = `p`.`ID_MARCA`)) join `categoria` `c` on(`c`.`ID_CATEGORIA` = `p`.`ID_CATEGORIA`)) where `pe`.`DS_STATUS` <> 'Cancelado' */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_vendas_produtos`
--

/*!50001 DROP VIEW IF EXISTS `vw_vendas_produtos`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_vendas_produtos` AS select `p`.`ID_PRODUTO` AS `ID_PRODUTO`,`p`.`NM_PRODUTO` AS `NM_PRODUTO`,`m`.`NM_MARCA` AS `NM_MARCA`,`c`.`ID_CATEGORIA` AS `ID_CATEGORIA`,`c`.`NM_CATEGORIA` AS `NM_CATEGORIA`,sum(`pi`.`QT_PRODUTO`) AS `QT_VENDIDA`,sum(`fn_calcular_subtotal`(`pi`.`QT_PRODUTO`,`pi`.`VL_UNITARIO`)) AS `VL_FATURADO` from ((((`pedido_item` `pi` join `pedido` `pe` on(`pe`.`ID_PEDIDO` = `pi`.`ID_PEDIDO`)) join `produto` `p` on(`p`.`ID_PRODUTO` = `pi`.`ID_PRODUTO`)) join `marca` `m` on(`m`.`ID_MARCA` = `p`.`ID_MARCA`)) join `categoria` `c` on(`c`.`ID_CATEGORIA` = `p`.`ID_CATEGORIA`)) where `pe`.`DS_STATUS` <> 'Cancelado' group by `p`.`ID_PRODUTO`,`p`.`NM_PRODUTO`,`m`.`NM_MARCA`,`c`.`ID_CATEGORIA`,`c`.`NM_CATEGORIA` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-13 16:54:52
/*
    CTE - Relatório de vendas por categoria

    Esta consulta utiliza uma CTE para organizar os dados de vendas
    por categoria, calculando a quantidade total vendida e o
    faturamento de cada categoria.

    A CTE facilita a organização da consulta antes da seleção final.
*/

USE `projeto-terceiro-bimestre-hotd`;

WITH vendas_categoria AS (
    SELECT
        c.ID_CATEGORIA,
        c.NM_CATEGORIA,
        SUM(pi.QT_PRODUTO) AS QT_VENDIDA,
        SUM(fn_calcular_subtotal(
            pi.QT_PRODUTO,
            pi.VL_UNITARIO
        )) AS VL_FATURADO
    FROM pedido_item pi
    INNER JOIN pedido pe
        ON pe.ID_PEDIDO = pi.ID_PEDIDO
    INNER JOIN produto p
        ON p.ID_PRODUTO = pi.ID_PRODUTO
    INNER JOIN categoria c
        ON c.ID_CATEGORIA = p.ID_CATEGORIA
    WHERE pe.DS_STATUS <> 'Cancelado'
    GROUP BY
        c.ID_CATEGORIA,
        c.NM_CATEGORIA
)
SELECT
    ID_CATEGORIA,
    NM_CATEGORIA,
    QT_VENDIDA,
    VL_FATURADO
FROM vendas_categoria
ORDER BY VL_FATURADO DESC;