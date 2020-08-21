<link href="pedidos/css/pedidos.css" rel="stylesheet">
<script src="pedidos/js/pedidos.js"></script>

<?php

if(isset($_GET["pedido"]) && ($_GET["pedido"] == 'ok')) {
  echo "
  <div class='alert alert-success' role='alert'>
    Pedido realizado com sucesso!
  </div>";  
}
else if(isset($_GET["pedido"]) && ($_GET["pedido"] == 'falha')) {
  echo "
  <div class='alert alert-danger' role='alert'>
    Houve um erro ao inserir o Pedido!
    Por favor verifique as quantidades em estoque.
  </div>    
  ";    
} 
else if(isset($_GET["pedido"]) && ($_GET["pedido"] == 'cliente')) {
  echo "
  <div class='alert alert-danger' role='alert'>
    Houve um erro ao inserir o Pedido!
    Por favor informe o cliente.
  </div>    
  ";    
} 

$consultaPessoas = "
  SELECT
    id_pessoa,
    nome_pessoa
  FROM
    tb_pessoas
  WHERE 
    status_pessoa = 'A'
";

$queryPessoas = mysqli_query($conexao,$consultaPessoas);

$consultaProdutos = "
  SELECT
    id_produto,
    nome_produto
  FROM
    tb_produtos
";

$queryProdutos = mysqli_query($conexao,$consultaProdutos);


if(isset($_POST["valor"])){
  if(isset($_POST) && ($_POST["valor"] != "") && ($_POST["data"] != "")
  ){
    $idPessoa = $_POST["nome"];
    $valorPedido = str_replace(",",".",$_POST["valor"]);
    $data = formataData($_POST["data"]);
    $produtos = $_POST["produtos"];

    $insertPedido = "INSERT INTO tb_pedidos (id_pessoa, valor_pedido, data_pedido, info_pedidos) VALUES (".$idPessoa.",'".$valorPedido."','".$data."', '".$produtos."')";
    //echo $insertPedido;exit;
    $query = mysqli_query($conexao,$insertPedido);
    if(mysqli_affected_rows($conexao) > 0) {
      echo "
      <div class='alert alert-success' role='alert'>
        Pedido inserido com sucesso!
      </div>";
    } else {
      echo "
      <div class='alert alert-danger' role='alert'>
        Houve um erro ao inserir o Pedido!
      </div>    
      ";    
    }
  }
}
//<input class="form-control col-2" type="date" name="data"  id="example-date-input">

?>

<fieldset id="pedido"><legend>Área de Pedido</legend>
    <br>   
    <form action="./pedidos/carrinho.php" id="frmPedidos" method="POST">
    <div class="form-row">
      <label for="example-date-input" class="col-3 col-form-label">Data do Pedido</label>
      <div class="col-12">
        <input type="text" id="data" name="data" value="<?php echo date('d/m/Y');?>" required/>
      </div>
    </div>
    <br>
    
    <div class="form-row">
      <label for="InputNome" class="col-3 col-form-label">  Nome Cliente: </label>
      <div class="col-12">
        <select class="form-control col-4" id="idCliente" name="nome" required>
          <option value="selecione">Selecione o Cliente</option>
          <?php

              while($objetoPessoas = mysqli_fetch_object($queryPessoas)) {
                echo "<option value='".$objetoPessoas->id_pessoa."'>".$objetoPessoas->nome_pessoa."</option>";
              }
          
          ?>
        </select>
      </div>
    </div>
    <br>

    <div class="form-row">
        <label for="InputProdutos" class="col-3 col-form-label"> Informe os Produtos: </label>
        <div class="col-12">
          <select multiple="multiple" id="InputProdutos" name="InputProdutos[]" required>
            <?php
              while($objetoProdutos = mysqli_fetch_object($queryProdutos)) {
                echo "<option value='".$objetoProdutos->id_produto."'>".$objetoProdutos->nome_produto."</option>";
              }            
            ?>
          </select>  
        </div>
    </div>
  <br>
</form>
</fieldset>

<!-- Button trigger modal -->
<button id="btnPedir" type="button" class="btn btn-primary" data-toggle="modal" data-target="#pedidoModal">
  Pedir
</button>

<div id="modal-container">
  <!-- Modal -->
  <div class="modal fade" id="pedidoModal" tabindex="-1" role="dialog" aria-labelledby="pedidoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content pedido-modal">
        <div class="modal-header">
          <h5 class="modal-title" id="pedidoModalLabel">Carrinho</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div id="modal-carrinho"><!-- retorno do Post do Jquery (./pedidos/pedidos.js) --></div>
        </div>
        <!--
        <div class="modal-footer">
          <button id="cancelar-pedido" type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <button id="finalizar-pedido" type="button" class="btn btn-primary">Finalizar Pedido</button>
        </div>
        -->
      </div>
    </div>
  </div>
</div>