<?php

$consultaProdutos = "
SELECT
  id_produto,
  nome_produto
FROM
  tb_produtos
";

$queryRemessa = mysqli_query($conexao,$consultaProdutos);

if(isset($_POST["idProduto"])){
  if(isset($_POST) && 
    ($_POST["qntAdicionada"] != "") &&
    ($_POST["data"] != "")
  ) {

    $idProduto = $_POST["idProduto"];
    $qntAdicionada = ($_POST["qntAdicionada"]);
    $data = formataData($_POST["data"]);

    $insertRemessa = "
      INSERT INTO
        tb_movimentacao (id_produto, tipo_movimentacao, qnt_produto, data_movimentacao) 
      VALUES 
        (".$idProduto.",'E','".$qntAdicionada."','".$data."')
    ";

    $updateProdutos = " 
      UPDATE tb_produtos SET 
        qnt_estoque = qnt_estoque + ".$qntAdicionada."
      WHERE 
        id_produto = ".$idProduto."
      ";
    
      //echo $updateProdutos;exit;
      //echo $insertRemessa;exit;

    $query = mysqli_query($conexao,$insertRemessa);
    if(mysqli_affected_rows($conexao) > 0) {
      $queryProdutos = mysqli_query($conexao,$updateProdutos);
      if(mysqli_affected_rows($conexao) > 0){
        echo "
        <div class='alert alert-success' role='alert'>
          Estoque atualizado com sucesso!
        </div>";
      }
      else{
        echo "
        <div class='alert alert-danger' role='alert'>
          Houve um erro ao atualizar o estoque!
        </div>    
        ";    
      }
      
    } else {
      echo "
      <div class='alert alert-danger' role='alert'>
        Houve um erro ao realizar entrada!
      </div>    
      ";    
    }
  }
}

?>

<fieldset id="pedido"><legend>Cadastro de Estoque</legend>
    <br>   
    <form form action="index.php?pagina=estoque" id="estoque" method="POST">
    <div class="form-row">
        <label for="example-date-input" class="col-3 col-form-label">Data de inclusão</label>
      <div class="col-12">
        <input type="text" name="data" value="<?php echo date('d/m/Y');?>" />
      </div>
    </div>
    <br>
    
    <div class="form-row">
      <label for="InputNome" class="col-3 col-form-label">  Nome do Produto: </label>
      <div class="col-12">
        <select class="form-control col-4" name="idProduto">
          <option>Selecione o Produto</option>

          <?php

            while($objetoProdutos = mysqli_fetch_object($queryRemessa)) {
              echo "<option value='".$objetoProdutos->id_produto."'>".$objetoProdutos->nome_produto."</option>";
            }

          ?>

        </select>
      </div>
    </div>
    <br>


    <div class="form-row">
        <label for="InputValor" class="col-3 col-form-label"> Nova remessa: </label>
        <div class="col-12">
          <input type="text" class="form-control col-2" name="qntAdicionada" placeholder="Quantidade comprada">
        </div>
    </div>

  <br>
  <button type="submit" class="btn btn-secondary">Concluir</button>
</form>
</fieldset>
