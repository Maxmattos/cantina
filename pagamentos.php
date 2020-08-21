<?php

$consultaPagamentos = "
SELECT
  id_pessoa,
  nome_pessoa
FROM
  tb_pessoas
WHERE 
  status_pessoa = 'A'
";

$queryPagamentos = mysqli_query($conexao,$consultaPagamentos);

if(isset($_POST["valorPago"])){
  if(isset($_POST) && 
    ($_POST["valorPago"] != "") &&
    ($_POST["data"] != "")
  ) {

    $idPessoa = $_POST["nome"];
    $valorPago = str_replace(",",".",$_POST["valorPago"]);
    $data = formataData($_POST["data"]);

    $insertPagamento = "INSERT INTO tb_pagamentos (id_pessoa, valor_pago, data_pagamento) VALUES (".$idPessoa.",'".$valorPago."','".$data."')";
    //echo $insertPagamento;exit;
    $query = mysqli_query($conexao,$insertPagamento);
    if(mysqli_affected_rows($conexao) > 0) {
      echo "
      <div class='alert alert-success' role='alert'>
        Pagamento inserido com sucesso!
      </div>";
    } else {
      echo "
      <div class='alert alert-danger' role='alert'>
        Houve um erro ao inserir o Pagamento!
      </div>    
      ";    
    }
  }
}

?>

<fieldset id="pedido"><legend>Área de Pagamento</legend>
    <br>   
    <form form action="index.php?pagina=pagamentos" id="pagamentos" method="POST">
    <div class="form-row">
        <label for="example-date-input" class="col-3 col-form-label">Data do Pagamento</label>
      <div class="col-12">
        <input type="text" name="data" value="<?php echo date('d/m/Y');?>" />
      </div>
    </div>
    <br>
    
    <div class="form-row">
      <label for="InputNome" class="col-3 col-form-label">  Nome Cliente: </label>
      <div class="col-12">
        <select class="form-control col-4" name="nome">
          <option>Selecione o Cliente</option>

          <?php

            while($objetoPessoas = mysqli_fetch_object($queryPagamentos)) {
              echo "<option value='".$objetoPessoas->id_pessoa."'>".$objetoPessoas->nome_pessoa."</option>";
            }

          ?>

        </select>
      </div>
    </div>
    <br>


    <div class="form-row">
        <label for="InputValor" class="col-3 col-form-label"> Valor Pago R$: </label>
        <div class="col-12">
          <input type="text" class="form-control col-2" name="valorPago" placeholder="Valor total pago">
        </div>
    </div>

  <br>
  <button type="submit" class="btn btn-secondary">Pagar</button>
</form>
</fieldset>
