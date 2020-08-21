<?php 

include ("connect.php");

function FormataData($data){
    // transforma 12/09/2019 para 2019-09-12
    $ano = substr($data,6,4);
    $mes = substr($data,3,2);
    $dia = substr($data,0,2);
    
    $dataFormatada = $ano.'-'.$mes.'-'.$dia;
    return $dataFormatada;
}

function FormataDataInvertida($data){    
    // transforma 2019-09-12 para 12/09/2019
    if($data == "") {
        $dataFormatada = "---";
    }
    else{
    $ano = substr($data,0,4);
    $mes = substr($data,5,2);
    $dia = substr($data,8,2);
    
    $dataFormatada = $dia.'/'.$mes.'/'.$ano;
    }
    return $dataFormatada;
}

function FormataValorBR($valor) {
  $valorBR = number_format($valor,2);
  $valorBR = str_replace('.',',',$valorBR);
  return $valorBR;
}

function ValidaEstoque($conexao,$idProduto, $qntProduto) {
  $ConfereEstoque = "
    select
      coalesce(qnt_estoque,0) as qnt_estoque,
      nome_produto
    from
      tb_produtos
    where
      id_produto = ".$idProduto."
  ";
  //echo $ConfereEstoque;exit;

  $queryConfereEst = mysqli_query($conexao,$ConfereEstoque);
  $retornoConfereEst = mysqli_affected_rows($conexao);
  if($retornoConfereEst > 0) {
    $objetoProduto = mysqli_fetch_object($queryConfereEst);
    //echo $objetoProduto->qnt_estoque." < ".$qntProduto;exit;
    if($objetoProduto->qnt_estoque < $qntProduto) {
      return false;
    }
    else {
      return true;
    }
  }
  else {
    return false;
  }
}
//$data = ($_POST["data"]);
//$dataFormatada = FormataData($data);
//  06/09/2019

?>