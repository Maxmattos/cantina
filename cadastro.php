<?php


@ $consultaPessoas = "
  SELECT
    id_pessoa,
    nome_pessoa
  FROM
    tb_pessoas
  WHERE 
    nome_pessoa = '".($_POST["nome"])."'
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



 

?>

<fieldset id="pedido"><legend>Área de Cadastro</legend>
<form action="index.php?pagina=cadastro" id="cadastro" method="POST">
 <br>
  <div class="form-row">
    <div class="col-4 col-form-label">
    <label for="InputNome">  Cadastrar Nome: </label>
        <input type="text" class="form-control" name="nome" placeholder="Nome Completo">
    </div>
  </div>
  <br>
  <input type="submit" class="btn btn-secondary" value="Cadastrar">
</form>
</fieldset>