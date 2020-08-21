<?php

$botaoCancelar = '';
$nomePessoa = '';
$textoBotaoCadastrar = 'Cadastrar';

if(isset($_GET["acao"]) && $_GET["acao"] == 'editar') {
  $textoBotaoCadastrar = 'Salvar';
  $acao = $_GET["acao"];
  $id = $_GET["id_pessoa"];
  $botaoCancelar = "<button onclick=\"location.href='index.php?pagina=clientes'\" type='button' class='btn btn-danger'>Cancelar</button>";
  $consultaEditarPessoa = "
    SELECT
      id_pessoa,
      nome_pessoa
    FROM
      tb_pessoas
    WHERE 
      id_pessoa = '".($_GET["id_pessoa"])."'
  ";

  $queryEditarPessoa = mysqli_query($conexao,$consultaEditarPessoa);
  $retornoEditarPessoa = mysqli_num_rows($queryEditarPessoa);
  $objetoConsultaPessoa = mysqli_fetch_object($queryEditarPessoa);

  $nomePessoa = $objetoConsultaPessoa->nome_pessoa;
  //echo "ID: ".$_GET["id_pessoa"];
  //echo "Nome: ".$nomePessoa;exit;
}

if(isset($_GET["acao"]) && $_GET["acao"] == 'excluir') {
  $consultaExcluirPessoa = "
    DELETE FROM
      tb_pessoas
    WHERE
      id_pessoa = '".($_GET["id_pessoa"])."'
  ";

  $queryExcluirPessoa = mysqli_query($conexao,$consultaExcluirPessoa);

  if(!$queryExcluirPessoa)
  {
    throw new Exception(mysqli_error($conexao));
    
  }
  //$retornoExcluirPessoa = mysqli_num_rows($queryExcluirPessoa);
  //$objetoConsultaPessoa = mysqli_fetch_object($queryExcluirPessoa);
  $objetoConsultaPessoa = '';
  //echo $nomePessoa;exit;
  //echo $queryExcluirPessoa;exit;

  if($queryExcluirPessoa == 0){
    $nomePessoa = $objetoConsultaPessoa->nome_pessoa;
  }
}

if (isset($_POST["nome"])) {
  $nome = $_POST["nome"];
  $consultaPessoas = "
    SELECT
      id_pessoa,
      nome_pessoa
    FROM
      tb_pessoas
    WHERE 
      nome_pessoa = '".$nome."'
  ";

  $queryPessoas = mysqli_query($conexao,$consultaPessoas);
  $retornoQueryPessoa = mysqli_num_rows($queryPessoas);

  if($retornoQueryPessoa > 0){
    echo "
      <div class='alert alert-danger' role='alert'>
      Cliente já existe!
      </div>    
      ";    
  }
  else {
    if(isset($_POST["nome"]) && ($_POST["nome"] != "")) {
      if($_POST["acao"] == 'editar') {
        // update
        $updateNome = "update tb_pessoas set nome_pessoa = '".$_POST["nome"]."' where id_pessoa = ".$_POST["id_pessoa"]."";  
        
        $query = mysqli_query($conexao,$updateNome);
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
        $insertNome = "INSERT INTO tb_pessoas (nome_pessoa) VALUES ('".$_POST["nome"]."')";  
        $query = mysqli_query($conexao,$insertNome);
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

<fieldset id="pedido"><legend>Cadastro de Clientes</legend>
<form action="index.php?pagina=clientes" id="cadastro" method="POST">
 <br>
  <div class="form-row">
    <div class="col-4 col-form-label">
    <label for="InputNome">  Cadastrar Nome: </label>
        <input type="text" class="form-control" name="nome" placeholder="Nome Completo" value="<?php echo $nomePessoa; ?>">
    </div>
  </div>
  <br>
  <input type="submit" class="btn btn-primary" value="<?php echo $textoBotaoCadastrar; ?>">  
  <input name="id_pessoa" type="hidden" class="btn btn-secondary" value="<?php echo $id; ?>">    
  <input name="acao" type="hidden" class="btn btn-secondary" value="<?php echo $acao; ?>">
  <?php echo $botaoCancelar; ?>   
</form>
</fieldset>
<br>
<?php
$consultaPessoas = "
  SELECT
    id_pessoa,
    nome_pessoa
  FROM
    tb_pessoas
  WHERE 
    coalesce(status_pessoa,'A') = 'A'
";

$queryPessoas = mysqli_query($conexao,$consultaPessoas);
$retornoQueryPessoa = mysqli_num_rows($queryPessoas);
if($retornoQueryPessoa == 0){
  echo "
    <div class='alert alert-danger' role='alert'>
     Nenhum cliente encontrado!
    </div>    
    ";    
}
else {

?>

  <table class="table table-striped table-bordered mydatatable" style="width: 100%" id="mydatatable">
    <thead>
      <tr>
        <th scope="col">Nome</th>
        <th scope="col">Ações</th>
      </tr>
    </thead>
    <tbody>
      <script>
        function ConfirmaExclusao(id_pessoa, nome_pessoa){
              if (confirm('Deseja realmente excluir '+nome_pessoa+'?')){
                window.location.href = 'index.php?pagina=clientes&id_pessoa='+id_pessoa+'&acao=excluir';
              }
              else {
                return false;
              }
            }
        </script>
      <?php
        //<th scope='row'>".$objetoConsulta->id_pessoa."</th>
        while($objetoConsulta = mysqli_fetch_object($queryPessoas)) {
          echo "          
            <tr>
              <td>".$objetoConsulta->nome_pessoa."</td>
              <td>
                <a href='index.php?pagina=clientes&id_pessoa=".$objetoConsulta->id_pessoa."&acao=editar'><button type='button' class='btn btn-primary'> <i class='fas fa-edit'></i></button></a>
                <a onclick=\"ConfirmaExclusao(".$objetoConsulta->id_pessoa.",'".$objetoConsulta->nome_pessoa."');\" href='#'><button type='button' class='btn btn-danger'><i class='fas fa-trash-alt'></i></button></a>
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