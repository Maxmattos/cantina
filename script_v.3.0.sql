alter table tb_movimentacao add id_pessoa int(11);
alter table tb_movimentacao add CONSTRAINT `fk_pessoa` FOREIGN KEY (`id_pessoa`) REFERENCES `tb_pessoas` (`id_pessoa`);

alter table tb_movimentacao add id_pedido int(11);
alter table tb_movimentacao add CONSTRAINT `fk_pedido_mov` FOREIGN KEY (`id_pedido`) REFERENCES `tb_pedidos` (`id_pedido`);