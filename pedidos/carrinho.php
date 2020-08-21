<?php    

// em arquivos de Ajax/Jquery, é necessário fazer os inclues mesmo que eles já
// tenham sido incluídos na index
include ("../connect.php");
include ("../funcoes.php");

$totalProduto = 0;
$totalPedido = 0;

// $_POST["produtos"] vem do Jquery .post()
if((isset($_POST["produtos"])) && ($_POST["produtos"] != "")) {
  $data = $_POST["data"];
  $idPessoa = $_POST["cliente"];
  $produtos = $_POST["produtos"];
  //print_r($_POST);exit;
  echo "  
  <form action='./pedidos/carrinho.php' id='frmFinPedido' method='POST'> 
  ";
    foreach($produtos as $produto) { 
      $consultaProduto = "
      SELECT
        id_produto,
        nome_produto,
        valor_produto,
        qnt_estoque
      FROM
        tb_produtos
      WHERE 
        id_produto = ".($produto)."
      ";
      $queryProduto = mysqli_query($conexao,$consultaProduto);
      $retornoProduto = mysqli_num_rows($queryProduto);
      if($retornoProduto > 0) {
        $objetoProduto = mysqli_fetch_object($queryProduto);
        if($objetoProduto->qnt_estoque <= 15 && $objetoProduto->qnt_estoque > 10 ) {
          $cor = 'orange';
        }
        else if($objetoProduto->qnt_estoque <= 10){
          $cor = 'red';
        }
        else{
          $cor = '#999';
        }
        
        $totalProduto = $objetoProduto->valor_produto;
        $totalPedido = $totalPedido + $totalProduto;

        echo "
          <div class='form-row'>
            <div class='col'>
              <b>".$objetoProduto->nome_produto."</b></br></br>
            </div>
            <div class='col'>
              <input type='hidden' name='id_produto[]' value=".$objetoProduto->id_produto.">
              <input type='hidden' name='nome_produto[]' value=".$objetoProduto->nome_produto.">
              <input type='hidden' name='valor_produto[]' value=".$objetoProduto->valor_produto.">
              <input type='text' class='form-control qnt-produto' id='".$objetoProduto->valor_produto."' alt=".$objetoProduto->qnt_estoque." name='qnt_produto[]' title='Digite a quantidade do produto' value='1' required>
            </div>
            <div class='col'>
              <label class='em-estoque' style='color: ".$cor.";'>Quantidade em estoque: ".$objetoProduto->qnt_estoque."</label>
            </div>
          </div>";   
      }
    }
  echo "
    <input type='hidden' name='data_pedido' value=".$data.">
    <input type='hidden' name='pessoa_pedido' value=".$idPessoa.">
    <input type='hidden' id='vlr_total-pedido' name='vlr_total-pedido' value=".$totalPedido.">
    <input type='hidden' name='hdnFinPedido'/>
  </form>
  <hr>
    <div id='aviso-estoque' style='display: none;' class='alert alert-danger' role='alert'><!-- retorno Jquery --></div>  
  <div class='form-row'>
    <div class='col'>
      <b>Total do Pedido: </b><label id='lblTotalPedido'>R$ ".number_format($totalPedido,2)."</label>
    </div>  
  </div>

  <div class='form-row'>
    <div class='col'>
      <div class='modal-footer'>
        <button id='cancelar-pedido' type='button' class='btn btn-secondary' data-dismiss='modal'>Cancelar</button>
        <button id='finalizar-pedido' type='button' class='btn btn-primary'>Finalizar Pedido</button>    
      </div>  
    </div> 
  </div>";    
}
else if(isset($_POST["hdnFinPedido"])) {
  foreach($_POST["id_produto"] as $key => $idProduto){
    // valida Estoque
    if(!ValidaEstoque($conexao,$idProduto,$_POST["qnt_produto"][$key])) {
      header("Location: ../index.php?pagina=pedidos&pedido=falha");
      exit;
    }
  }
  $inserePedido = "
  insert into tb_pedidos
    (id_pessoa, valor_pedido, info_pedidos, data_pedido)
  values
    (".$_POST["pessoa_pedido"].",".$_POST["vlr_total-pedido"].",'','".FormataData($_POST["data_pedido"])."');
  ";
  //echo $inserePedido;exit;

  $queryInserePed = mysqli_query($conexao,$inserePedido);
  $retornoInserePed = mysqli_affected_rows($conexao);

  if($retornoInserePed > 0) {
    $idPedido = mysqli_insert_id($conexao);
    foreach($_POST["id_produto"] as $key => $idProduto){  
      $totalProdFinal = $_POST["qnt_produto"][$key] * $_POST["valor_produto"][$key];
      //$totalPedido = $totalPedido + $totalProdFinal;
      $insereMovimentoEstoque = "
        insert into tb_movimentacao 
          (id_produto, tipo_movimentacao, qnt_produto, data_movimentacao, id_pessoa, id_pedido)
        values
          (".$idProduto.",'S',".$_POST["qnt_produto"][$key].",'".FormataData($_POST["data_pedido"])."', ".$_POST["pessoa_pedido"].", ".$idPedido.");
      ";
      //echo $insereMovimentoEstoque."<br>";
  
      $queryInsereMov = mysqli_query($conexao,$insereMovimentoEstoque);
      $retornoInsereMov = mysqli_affected_rows($conexao);
  
      if($retornoInsereMov > 0) {
        $SaidaEstoqueProduto = "
          update tb_produtos set
            qnt_estoque = qnt_estoque - ".$_POST["qnt_produto"][$key]."
          where
            id_produto = ".$idProduto.";
        ";
        //echo $SaidaEstoqueProduto."<br>";
        $querySaidaProduto = mysqli_query($conexao,$SaidaEstoqueProduto);
        $retornoSaidaProduto = mysqli_affected_rows($conexao);
        if($retornoSaidaProduto > 0) {
          header("Location: ../index.php?pagina=pedidos&pedido=ok");
        }
        else {
          echo "
          <div class='alert alert-danger' role='alert'>
            Erro ao realizar saída do produto <b>".$_POST["nome_produto"][$key]."</b>!
          </div>    
          "; 
        }
      }
      else {
        echo "
        <div class='alert alert-danger' role='alert'>
          Erro ao inserir movimento para o produto <b>".$_POST["nome_produto"][$key]."</b>!
        </div>    
        ";     
        $deletePedido = "
          delete
          from
            tb_pedidos
          where
          tb_pedidos.id_pedido = ".$idPedido.";
        ";
        $queryDeleteProduto = mysqli_query($conexao,$deletePedido);
        //echo $deletePedido;exit;
      }
      /*
      **** sessão de debug ***  descomentar para debuggar :) ****
      echo "
        Posição: ".$key."<br>
        ID: ".$idProduto." <br>
        Nome: ".$_POST["nome_produto"][$key]." <br>
        Valor: R$ ".$_POST["valor_produto"][$key]."<br>
        Quantidade:".$_POST["qnt_produto"][$key]."<br>
        <b>Total</b>: ".$totalProdFinal."<br><br><br>
      ";
      */
    }
  }
  else {
    /*echo "
    <div class='alert alert-danger' role='alert'>
      Erro ao inserir Pedido! <br>
      Nome do Cliente: ".$_POST["pessoa_pedido"].". <br>
      Verifique os dados preenchidos no formulário de pedido.
    </div>    
    "; */ 
    header("Location: ../index.php?pagina=pedidos&pedido=cliente");
  }
}
else {
  echo "
  <div class='alert alert-danger' role='alert'>
    Por favor preencha todos os dados!
  </div>    
  ";        
}
?>
<script>
$(document).ready(function() {
    function CalcularNovoTotal() {
      var valor = 0;
      $(".qnt-produto").each(function(){
        $("#finalizar-pedido").show();
        //$("#aviso-estoque").hide();
        valor = valor + $(this).val() * $(this).attr("id");
      });
      return valor;
    }

    $(".qnt-produto").keyup(function(){
      var TotalPedido = CalcularNovoTotal();
      $("#lblTotalPedido").text('R$ '+TotalPedido.toFixed(2));
      $("#vlr_total-pedido").val(TotalPedido.toFixed(2));
    });

    $(".qnt-produto").blur(function(){
      var TotalPedido = CalcularNovoTotal();
      $("#lblTotalPedido").text('R$ '+TotalPedido.toFixed(2));
      $("#vlr_total-pedido").val(TotalPedido.toFixed(2));
    });    
    
    $("#finalizar-pedido").click(function(){
      $("#frmFinPedido").submit();
    });
});
</script>