<?php

$botaoCancelar = '';
$nomeProduto = '';
$valorProduto = '';
$qntProduto = '';
$textoBotaoCadastrar = 'Cadastrar';
$display = 'block';

if(isset($_GET["acao"]) && $_GET["acao"] == 'editar') {
  $textoBotaoCadastrar = 'Salvar';
  $acao = $_GET["acao"];
  $display = 'none';
  $id = $_GET["id_produto"];
  $botaoCancelar = "<button onclick=\"location.href='index.php?pagina=produtos'\" type='button' class='btn btn-danger'>Cancelar</button>";
  $consultaEditarProduto = "
  SELECT
    id_produto,
    nome_produto,
    valor_produto,
    qnt_estoque
  FROM
    tb_produtos
  WHERE 
    id_produto = '".($_GET["id_produto"])."'
";

  $queryEditarProduto = mysqli_query($conexao,$consultaEditarProduto);
  $retornoEditarProduto = mysqli_num_rows($queryEditarProduto);
  $objetoConsultaProduto = mysqli_fetch_object($queryEditarProduto);

  $nomeProduto = $objetoConsultaProduto->nome_produto;
  $valorProduto = $objetoConsultaProduto->valor_produto;
  $qntProduto = $objetoConsultaProduto->qnt_estoque;
  //echo "ID: ".$_GET["id_produto"];
  //echo "Nome: ".$nomeProduto;exit;
}

if(isset($_GET["acao"]) && $_GET["acao"] == 'excluir') {
  $consultaExcluirProduto = "
    DELETE FROM
      tb_produtos
    WHERE
      id_produto = '".($_GET["id_produto"])."'
  ";

  $queryExcluirProduto = mysqli_query($conexao,$consultaExcluirProduto);

  if(!$queryExcluirProduto)
  {
    //throw new Exception(mysqli_error($conexao));
    echo "
      <div class='alert alert-danger' role='alert'>
      Produto já foi utilizado em outros pedidos!
      </div>    
    ";
    
  }
  else {
    echo "
      <div class='alert alert-success' role='alert'>
        Registro excluído com sucesso!
      </div>
    ";
  }
}

if (isset($_POST["nome"]) && ($_POST["valor"]) && ($_POST["quantidade"])) {
  
  $nome = $_POST["nome"];
  $valor = str_replace('.',',',$_POST["valor"]);
  $quantidade = $_POST["quantidade"];
  
  $consultaProdutos = "
    SELECT
      id_produto,
      COALESCE (nome_produto,''),
      COALESCE (valor_produto,0),
      COALESCE (qnt_estoque,0)
    FROM
      tb_produtos
    WHERE 
      nome_produto = '".$nome."'
    ";

  $queryProdutos = mysqli_query($conexao,$consultaProdutos);
  $retornoQueryProduto = mysqli_num_rows($queryProdutos);

  if(($retornoQueryProduto > 0) && ($_POST["acao"] != 'editar')){
    echo "
      <div class='alert alert-danger' role='alert'>
      Produto já existe!
      </div>    
      ";    
  }
  else {
    if(isset($_POST["nome"]) && ($_POST["nome"] != "")) {
      if($_POST["acao"] == 'editar') {
        // update
        $updateProduto = "
        UPDATE 
          tb_produtos 
        SET 
          nome_produto = '".$_POST["nome"]."',
          valor_produto = '".str_replace(',','.',$_POST["valor"])."',
          qnt_estoque = '".$_POST["quantidade"]."' 
        WHERE 
          id_produto = ".$_POST["id_produto"]."
        ";
        //echo $updateProduto;exit;
        
        $query = mysqli_query($conexao,$updateProduto);
        if(mysqli_affected_rows($conexao)) {
          echo "
          <div class='alert alert-success' role='alert'>
            Registro atualizado com sucesso!
          </div>";
        } else {
          echo "
          <div class='alert alert-danger' role='alert'>
            Houve um erro ao atualizar o registro!
          </div>    
          ";    
        }
      }
      else {
        $insertProduto = "
        INSERT INTO
          tb_produtos (nome_produto,valor_produto,qnt_estoque) 
        VALUES
         (
          '".$_POST["nome"]."',
          '".str_replace(',','.',$_POST["valor"])."',
          '".$_POST["quantidade"]."'
        )
        ";
        //echo $insertProduto;exit;

        $query = mysqli_query($conexao,$insertProduto);
        if(mysqli_affected_rows($conexao)) {
          echo "
          <div class='alert alert-success' role='alert'>
            Registro inserido com sucesso!
          </div>";
        } else {
          echo "
          <div class='alert alert-danger' role='alert'>
            Houve um erro ao inserir o registro!
          </div>    
          ";    
        }
      }
    }
  }
} //isset $_POST["nome"]
?>

<fieldset id="pedido"><legend>Cadastrar Produtos</legend>
<form action="index.php?pagina=produtos" id="produto" method="POST">
 <br>
  <div class="form-row">
    <div class="col-4 col-form-label">
        <label for="InputNome"> Produto: </label>
        <input type="text" class="form-control" name="nome" placeholder="Nome do Produto" value="<?php echo $nomeProduto; ?>" required>
    </div>
  </div>
  <div class="form-row">
    <div class="col-4 col-form-label">
        <label for="InputNome">  Valor Unitário: </label>
        <input type="text" class="form-control" name="valor" placeholder="Valor do Produto" value="<?php echo str_replace('.',',', $valorProduto); ?>" required>
    </div>
  </div>
  <div class="form-row" style='display:<?php echo $display; ?>'>
    <div class="col-4 col-form-label">
        <label for="InputNome">  Quantidade: </label>
        <input type="text" class="form-control" name="quantidade" placeholder="Quantidade do Produto" value="<?php echo $qntProduto; ?>" required>
    </div>
  </div>
  <br>
  <input type="submit" class="btn btn-primary" value="<?php echo $textoBotaoCadastrar; ?>">  
  <input name="id_produto" type="hidden" class="btn btn-secondary" value="<?php echo $id; ?>">    
  <input name="acao" type="hidden" class="btn btn-secondary" value="<?php echo $acao; ?>">
  <?php echo $botaoCancelar; ?>   
</form>
</fieldset>
<br>
<?php
$consultaProdutos = "
  SELECT
    id_produto,
    nome_produto,
    valor_produto,
    qnt_estoque
  FROM
    tb_produtos
  ";

$queryProdutos = mysqli_query($conexao,$consultaProdutos);
$retornoQueryProduto = mysqli_num_rows($queryProdutos);
if($retornoQueryProduto == 0){
  echo "
    <div class='alert alert-danger' role='alert'>
     Nenhum produto encontrado!
    </div>    
    ";    
}
else {

?>

  <table class="table table-striped table-bordered mydatatable" style="width: 100%" id="mydatatable">
    <thead>
      <tr>
        <th scope="col">Produto</th>
        <th scope="col">Valor Unitário (R$)</th>
        <th scope="col">Quantidade</th>
        <th scope="col">Ações</th>
      </tr>
    </thead>
    <tbody>
      <script>
        function ConfirmaExclusao(id_produto, nome_produto){
              if (confirm('Deseja realmente excluir '+nome_produto+'?')){
                window.location.href = 'index.php?pagina=produtos&id_produto='+id_produto+'&acao=excluir';
              }
              else {
                return false;
              }
            }
        </script>
      <?php
        //<th scope='row'>".$objetoConsulta->id_produto."</th>
        while($objetoConsulta = mysqli_fetch_object($queryProdutos)) {
          echo "          
            <tr>
              <td>".$objetoConsulta->nome_produto."</td>
              <td>". str_replace('.',',', $objetoConsulta->valor_produto)."</td>
              <td>".$objetoConsulta->qnt_estoque."</td>
              <td>
                <a href='index.php?pagina=produtos&id_produto=".$objetoConsulta->id_produto."&acao=editar'><button type='button' class='btn btn-primary'> <i class='fas fa-edit'></i></button></a>
                <a onclick=\"ConfirmaExclusao(".$objetoConsulta->id_produto.",'".$objetoConsulta->nome_produto."');\" href='#'><button type='button' class='btn btn-danger'><i class='fas fa-trash-alt'></i></button></a>
              </td>
            </tr>
          ";
        }
      ?>
    </tbody>
  </table>
  <script src="./DataTables/jquery-3.3.1.min.js"></script>
    <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/pooper.js/1.14.7/umd/popper.min.js"></script>-->
    <script src="./DataTables/bootstrap.min.js"></script>

    <script src="./DataTables/jquery.dataTables.min.js"></script>
    <script src="./DataTables/dataTables.bootstrap4.min.js"></script>
    <script>
        $('.mydatatable').dataTable();
  </script>

<?php 
} // fecha o else
?>