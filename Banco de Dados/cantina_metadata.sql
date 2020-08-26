-- --------------------------------------------------------
-- Servidor:                     127.0.0.1
-- Versão do servidor:           5.6.21 - MySQL Community Server (GPL)
-- OS do Servidor:               Win32
-- HeidiSQL Versão:              11.0.0.5919
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Copiando estrutura do banco de dados para cantina
CREATE DATABASE IF NOT EXISTS `cantina` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `cantina`;

-- Copiando estrutura para procedure cantina.sp_usuarios_insert
DELIMITER //
CREATE PROCEDURE `sp_usuarios_insert`(
pnome_pessoa VARCHAR(50),
pstatus_pessoa VARCHAR(1)
)
BEGIN

	INSERT INTO tb_pessoas (nome_pessoa, status_pessoa) VALUES (pnome_pessoa, pstatus_pessoa);
	
	SELECT * FROM tb_pessoas WHERE id_pessoa = LAST_INSERT_ID();

END//
DELIMITER ;

-- Copiando estrutura para tabela cantina.tb_itenspedido
CREATE TABLE IF NOT EXISTS `tb_itenspedido` (
  `id_itenspedido` int(11) NOT NULL AUTO_INCREMENT,
  `id_produto` int(11) DEFAULT NULL,
  `id_pedido` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_itenspedido`),
  KEY `fk_itens` (`id_produto`),
  KEY `fk_pedido` (`id_pedido`),
  CONSTRAINT `fk_itens` FOREIGN KEY (`id_produto`) REFERENCES `tb_produtos` (`id_produto`),
  CONSTRAINT `fk_pedido` FOREIGN KEY (`id_pedido`) REFERENCES `tb_pedidos` (`id_pedido`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Copiando dados para a tabela cantina.tb_itenspedido: ~0 rows (aproximadamente)
/*!40000 ALTER TABLE `tb_itenspedido` DISABLE KEYS */;
/*!40000 ALTER TABLE `tb_itenspedido` ENABLE KEYS */;

-- Copiando estrutura para tabela cantina.tb_movimentacao
CREATE TABLE IF NOT EXISTS `tb_movimentacao` (
  `id_movimentacao` int(11) NOT NULL AUTO_INCREMENT,
  `id_produto` int(11) DEFAULT NULL,
  `tipo_movimentacao` varchar(1) DEFAULT NULL,
  `qnt_produto` int(11) DEFAULT NULL,
  `data_movimentacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `id_pessoa` int(11) DEFAULT NULL,
  `id_pedido` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_movimentacao`),
  KEY `fk_produto` (`id_produto`),
  KEY `fk_pessoa` (`id_pessoa`),
  KEY `fk_pedido_mov` (`id_pedido`),
  CONSTRAINT `fk_pedido_mov` FOREIGN KEY (`id_pedido`) REFERENCES `tb_pedidos` (`id_pedido`),
  CONSTRAINT `fk_pessoa` FOREIGN KEY (`id_pessoa`) REFERENCES `tb_pessoas` (`id_pessoa`),
  CONSTRAINT `fk_produto` FOREIGN KEY (`id_produto`) REFERENCES `tb_produtos` (`id_produto`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8;


-- Copiando estrutura para tabela cantina.tb_pagamentos
CREATE TABLE IF NOT EXISTS `tb_pagamentos` (
  `id_pagamento` int(11) NOT NULL AUTO_INCREMENT,
  `id_pessoa` int(11) NOT NULL,
  `valor_pago` decimal(10,2) DEFAULT NULL,
  `data_pagamento` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pagamento`) USING BTREE,
  KEY `tb_pagamentos_ibfk_1` (`id_pessoa`) USING BTREE,
  CONSTRAINT `tb_pagamentos_ibfk_1` FOREIGN KEY (`id_pessoa`) REFERENCES `tb_pessoas` (`id_pessoa`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- Copiando estrutura para tabela cantina.tb_pedidos
CREATE TABLE IF NOT EXISTS `tb_pedidos` (
  `id_pedido` int(11) NOT NULL AUTO_INCREMENT,
  `id_pessoa` int(11) NOT NULL,
  `valor_pedido` decimal(10,2) DEFAULT NULL,
  `info_pedidos` varchar(100) DEFAULT NULL,
  `data_pedido` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pedido`) USING BTREE,
  KEY `tb_pedidos_ibfk_1` (`id_pessoa`) USING BTREE,
  CONSTRAINT `tb_pedidos_ibfk_1` FOREIGN KEY (`id_pessoa`) REFERENCES `tb_pessoas` (`id_pessoa`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- Copiando estrutura para tabela cantina.tb_pessoas
CREATE TABLE IF NOT EXISTS `tb_pessoas` (
  `id_pessoa` int(11) NOT NULL AUTO_INCREMENT,
  `nome_pessoa` varchar(50) DEFAULT NULL,
  `status_pessoa` varchar(1) DEFAULT 'A',
  PRIMARY KEY (`id_pessoa`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- Copiando estrutura para tabela cantina.tb_produtos
CREATE TABLE IF NOT EXISTS `tb_produtos` (
  `id_produto` int(11) NOT NULL AUTO_INCREMENT,
  `nome_produto` varchar(30) DEFAULT NULL,
  `valor_produto` varchar(10) DEFAULT NULL,
  `qnt_estoque` int(11) DEFAULT NULL,
  `obs_produto` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_produto`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- Copiando estrutura para função cantina.total_pagamento
DELIMITER //
CREATE FUNCTION `total_pagamento`(p_pessoa int) RETURNS decimal(10,2)
BEGIN
	declare v_total decimal(10,2);    
    SELECT 
        coalesce(sum(tb_pagamentos.valor_pago),0) 
    FROM 
        tb_pagamentos 
    where 
        tb_pagamentos.id_pessoa = p_pessoa
	INTO
    	v_total;
  RETURN v_total;    
END//
DELIMITER ;

-- Copiando estrutura para função cantina.total_pedido
DELIMITER //
CREATE FUNCTION `total_pedido`(p_pessoa int) RETURNS decimal(10,2)
BEGIN
	declare v_total decimal(10,2);    
    SELECT 
        coalesce(sum(tb_pedidos.valor_pedido),0) 
    FROM 
        tb_pedidos
    where 
        tb_pedidos.id_pessoa = p_pessoa
	INTO
    	v_total;
    RETURN v_total;    
END//
DELIMITER ;

-- Copiando estrutura para função cantina.ultima_compra
DELIMITER //
CREATE FUNCTION `ultima_compra`(p_pessoa INT) RETURNS timestamp
BEGIN
	declare data_compra TIMESTAMP;    
    SELECT 
        max(tb_pedidos.data_pedido) data_pedido
    FROM 
        tb_pedidos
    where 
        tb_pedidos.id_pessoa = p_pessoa
	INTO
    	data_compra;
    RETURN data_compra;    
END//
DELIMITER ;

-- Copiando estrutura para função cantina.ultimo_pagamento
DELIMITER //
CREATE FUNCTION `ultimo_pagamento`(p_pessoa INT) RETURNS timestamp
BEGIN
	declare data_pagamento TIMESTAMP;    
    SELECT 
        max(tb_pagamentos.data_pagamento) data_pagamento
    FROM 
        tb_pagamentos
    where 
        tb_pagamentos.id_pessoa = p_pessoa
	INTO
    	data_pagamento;
    RETURN data_pagamento;    
END//
DELIMITER ;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
