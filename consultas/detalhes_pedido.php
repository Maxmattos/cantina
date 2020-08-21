<style type="text/css">
.pagamento {
  display: none;
}
</style>
<?php

// em arquivos de Ajax/Jquery, é necessário fazer os inclues mesmo que eles já
// tenham sido incluídos na index
include ("../connect.php");
include ("../funcoes.php");

$idPessoa = $_POST["cliente"]; //vem do Jquery post

$consultaPedidos = "
SELECT
  tb_pedidos.id_pedido,
  tb_pedidos.data_pedido,
  tb_produtos.nome_produto,
  tb_movimentacao.qnt_produto,
  '' as valor_pedido
FROM
  tb_pedidos
  inner join tb_movimentacao on tb_movimentacao.id_pedido = tb_pedidos.id_pedido
  inner join tb_produtos on tb_produtos.id_produto = tb_movimentacao.id_produto
where
  tb_pedidos.id_pessoa = ".$idPessoa."
  
union ALL

SELECT
  'Pagamento' as id_pedido,
  tb_pagamentos.data_pagamento as data_pedido,
  '' as nome_produto,
  '' as qnt_produto,
  tb_pagamentos.valor_pago as valor_pedido
FROM
  tb_pagamentos
where
  tb_pagamentos.id_pessoa = ".$idPessoa."

union ALL

SELECT
  tb_pedidos.id_pedido,
  tb_pedidos.data_pedido,
  '' as nome_produto,
  '' as qnt_produto,
  tb_pedidos.valor_pedido
FROM
  tb_pedidos
  inner join tb_movimentacao on tb_movimentacao.id_pedido = tb_pedidos.id_pedido
  inner join tb_produtos on tb_produtos.id_produto = tb_movimentacao.id_produto
where
  tb_pedidos.id_pessoa = ".$idPessoa."
group by
	1
order by
  2, 1, 5  
";

$queryPedidos = mysqli_query($conexao,$consultaPedidos);
$retornoPedidos = mysqli_num_rows($queryPedidos);
if($retornoPedidos > 0){
?>
</br>
<table class="table table-striped table-bordered mydatatable" style="width: 100%" id="mydatatable">
    <thead>
      <tr>
        <th>N° Pedido</th>
        <th>Data do Pedido</th>
        <th>Produto</th>
        <th>Quantidade</th>
        <th>Valor</th>
      </tr>    
    </thead>
    <tbody>
      <?php                    
        while($objetoPedido = mysqli_fetch_object($queryPedidos)) {          
          $class = '';
          if($objetoPedido->valor_pedido != ""){
            if($objetoPedido->id_pedido == 'Pagamento'){
              $class = 'pagamento';
              $backgroundRow = "#d4edda";
            }
            else {
              $backgroundRow = "#dee2e6";
            }
          }  
          else {
            $backgroundRow = "#FFF";
          }
            echo "<tr class='".$class."' style='background-color: $backgroundRow'>";
                echo "<td>".$objetoPedido->id_pedido."</td>";
                echo "<td>".FormataDataInvertida($objetoPedido->data_pedido)."</td>";
                echo "<td>".$objetoPedido->nome_produto."</td>";
                echo "<td>".$objetoPedido->qnt_produto."</td>";
                if($objetoPedido->valor_pedido != ""){
                  echo "<td><b>Total: R$ ".FormataValorBR($objetoPedido->valor_pedido)."</b></td>";
                }
                else {
                  echo "<td>".$objetoPedido->valor_pedido."</td>";
                }
            echo "</tr>";
        }    
      ?>                
    </tbody>
    <!--
    <tfoot>
      <tr>
        <th>N° Pedido</th>
        <th>Data do Pedido</th>
        <th>Produto</th>
        <th>Quantidade</th>
        <th>Valor</th>
      </tr>
    </tfoot>  
    -->    
</table>
<div style="text-align: right; margin-right: 10px;">
  <?php 
    if($_POST['total'] == 0) {
      $cor = "blue";
    }
    else {
      $cor = "red";
    }
    echo "<b>Saldo Devedor: <span style='color: ".$cor."'> R$ ".FormataValorBR(abs($_POST['total']))."</span></b>"; 
  ?>
</div>
<?php
}
else{
  echo "<b> Este cliente não possui pedidos! </b>";
}
?>

<script>
$(document).ready(function() {
  if($('#exibePagamento')[0].checked) {
    $(".pagamento").show();
  }
  else {
    $(".pagamento").hide();
  }
  $("#exibePagamento").click(function(){    
    if($(this)[0].checked) {
      $(".pagamento").show("slow");
    }
    else {
      $(".pagamento").hide("slow");
    }
  });
});  
</script>