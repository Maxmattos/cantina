/*
 Navicat Premium Data Transfer

 Source Server         : Cantina Jovens
 Source Server Type    : MySQL
 Source Server Version : 50621
 Source Host           : localhost:3306
 Source Schema         : cantina

 Target Server Type    : MySQL
 Target Server Version : 50621
 File Encoding         : 65001

 Date: 03/02/2020 10:39:22
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for tb_itenspedido
-- ----------------------------
DROP TABLE IF EXISTS `tb_itenspedido`;
CREATE TABLE `tb_itenspedido`  (
  `id_itenspedido` int(11) NOT NULL AUTO_INCREMENT,
  `id_produto` int(11) NULL DEFAULT NULL,
  `id_pedido` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id_itenspedido`) USING BTREE,
  INDEX `fk_itens`(`id_produto`) USING BTREE,
  INDEX `fk_pedido`(`id_pedido`) USING BTREE,
  CONSTRAINT `fk_itens` FOREIGN KEY (`id_produto`) REFERENCES `tb_produtos` (`id_produto`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_pedido` FOREIGN KEY (`id_pedido`) REFERENCES `tb_pedidos` (`id_pedido`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for tb_movimentacao
-- ----------------------------
DROP TABLE IF EXISTS `tb_movimentacao`;
CREATE TABLE `tb_movimentacao`  (
  `id_movimentacao` int(11) NOT NULL AUTO_INCREMENT,
  `id_produto` int(11) NULL DEFAULT NULL,
  `tipo_movimentacao` varchar(1) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `qnt_produto` int(11) NULL DEFAULT NULL,
  `data_movimentacao` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP(0),
  `id_pessoa` int(11) NULL DEFAULT NULL,
  `id_pedido` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id_movimentacao`) USING BTREE,
  INDEX `fk_produto`(`id_produto`) USING BTREE,
  INDEX `fk_pessoa`(`id_pessoa`) USING BTREE,
  INDEX `fk_pedido_mov`(`id_pedido`) USING BTREE,
  CONSTRAINT `fk_pedido_mov` FOREIGN KEY (`id_pedido`) REFERENCES `tb_pedidos` (`id_pedido`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_pessoa` FOREIGN KEY (`id_pessoa`) REFERENCES `tb_pessoas` (`id_pessoa`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_produto` FOREIGN KEY (`id_produto`) REFERENCES `tb_produtos` (`id_produto`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 12 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for tb_pagamentos
-- ----------------------------
DROP TABLE IF EXISTS `tb_pagamentos`;
CREATE TABLE `tb_pagamentos`  (
  `id_pagamento` int(11) NOT NULL AUTO_INCREMENT,
  `id_pessoa` int(11) NOT NULL,
  `valor_pago` decimal(10, 2) NULL DEFAULT NULL,
  `data_pagamento` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pagamento`) USING BTREE,
  INDEX `tb_pagamentos_ibfk_1`(`id_pessoa`) USING BTREE,
  CONSTRAINT `tb_pagamentos_ibfk_1` FOREIGN KEY (`id_pessoa`) REFERENCES `tb_pessoas` (`id_pessoa`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for tb_pedidos
-- ----------------------------
DROP TABLE IF EXISTS `tb_pedidos`;
CREATE TABLE `tb_pedidos`  (
  `id_pedido` int(11) NOT NULL AUTO_INCREMENT,
  `id_pessoa` int(11) NOT NULL,
  `valor_pedido` decimal(10, 2) NULL DEFAULT NULL,
  `info_pedidos` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `data_pedido` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pedido`) USING BTREE,
  INDEX `tb_pedidos_ibfk_1`(`id_pessoa`) USING BTREE,
  CONSTRAINT `tb_pedidos_ibfk_1` FOREIGN KEY (`id_pessoa`) REFERENCES `tb_pessoas` (`id_pessoa`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 17 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for tb_pessoas
-- ----------------------------
DROP TABLE IF EXISTS `tb_pessoas`;
CREATE TABLE `tb_pessoas`  (
  `id_pessoa` int(11) NOT NULL AUTO_INCREMENT,
  `nome_pessoa` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `status_pessoa` varchar(1) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT 'A',
  PRIMARY KEY (`id_pessoa`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 33 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for tb_produtos
-- ----------------------------
DROP TABLE IF EXISTS `tb_produtos`;
CREATE TABLE `tb_produtos`  (
  `id_produto` int(11) NOT NULL AUTO_INCREMENT,
  `nome_produto` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `valor_produto` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `qnt_estoque` int(11) NULL DEFAULT NULL,
  `obs_produto` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id_produto`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Function structure for total_pagamento
-- ----------------------------
DROP FUNCTION IF EXISTS `total_pagamento`;
delimiter ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `total_pagamento`(p_pessoa int) RETURNS decimal(10,2)
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
END
;;
delimiter ;

-- ----------------------------
-- Function structure for total_pedido
-- ----------------------------
DROP FUNCTION IF EXISTS `total_pedido`;
delimiter ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `total_pedido`(p_pessoa int) RETURNS decimal(10,2)
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
END
;;
delimiter ;

-- ----------------------------
-- Function structure for ultima_compra
-- ----------------------------
DROP FUNCTION IF EXISTS `ultima_compra`;
delimiter ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `ultima_compra`(p_pessoa INT) RETURNS timestamp
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
END
;;
delimiter ;

-- ----------------------------
-- Function structure for ultimo_pagamento
-- ----------------------------
DROP FUNCTION IF EXISTS `ultimo_pagamento`;
delimiter ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `ultimo_pagamento`(p_pessoa INT) RETURNS timestamp
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
END
;;
delimiter ;

SET FOREIGN_KEY_CHECKS = 1;
