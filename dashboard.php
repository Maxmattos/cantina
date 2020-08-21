<?php

include "connect.php";

$ultimoPedidoGeral = "
SELECT
  coalesce(max(tb_pedidos.data_pedido),'0000-00-00') as data_pedido
FROM
  tb_pedidos
";
$queryUltimoPedido = mysqli_query($conexao,$ultimoPedidoGeral);
$objetoUltimoPedido = mysqli_fetch_object($queryUltimoPedido);
$dataUltimoPedido = FormataDataInvertida($objetoUltimoPedido->data_pedido);  

$consultaTotalVendido = 
  "SELECT 
    SUM(valor_pedido) AS total_vendido
  FROM 
    tb_pedidos
  ";

$queryTotalVendido = mysqli_query($conexao,$consultaTotalVendido);
$objetoTotalVendido = mysqli_fetch_object($queryTotalVendido);
$totalVendido = $objetoTotalVendido->total_vendido;

$consultaTotalPago = 
  "SELECT 
    SUM(valor_pago) AS total_pago
  FROM 
    tb_pagamentos
  ";

$queryTotalPago = mysqli_query($conexao,$consultaTotalPago);
$objetoTotalPago = mysqli_fetch_object($queryTotalPago);
$totalPago = $objetoTotalPago->total_pago;

$saldoCantina = $totalPago - $totalVendido;

?>


<!-- Demo scripts for this page-->
<link href="css/dashboard.css" rel="stylesheet" type="text/css">
<script src="vendor/chart.js/Chart.min.js"></script>

<!-- Area Chart Example-->

<div class="card mb-3">
  <div class="card-header">
    <i class="fas fa-chart-area"></i>
    Saldo Total da Cantina
  </div>
  <div class="row">
    <span class="total-cantina vendido"> 
      <span class="titulo-cantina">Valor Total Vendido</span><br>
      <?php 
        echo "R$ " . FormataValorBR($totalVendido);
      ?> 
    </span>
    <?php

        if($saldoCantina >= 0){
          $cor = "#007bff";
        }
        else{
          $cor = "#dc3545";
        }
       
       ?> 
    <span class="total-cantina saldo" style="color: <?php echo $cor;?>"> 
      <span class="titulo-cantina">Saldo da Cantina</span><br>
      <?php echo "R$ ". FormataValorBR($saldoCantina); ?>
    </span>
    <span class="total-cantina pago"> 
      <span class="titulo-cantina">Valor Total Pago</span><br>
      <?php echo "R$ " . FormataValorBR($totalPago);?> 
    </span> 
  </div>
  <div class="card-footer small text-muted">Último pedido em: <?php echo $dataUltimoPedido; ?></div>
</div>

<div class="card mb-3">
  <div class="card-header">
    <i class="fas fa-chart-area"></i>
    Pedidos</div>
  <div class="card-body">
    <canvas id="myAreaChart" width="100%" height="30"></canvas>
  </div>
  <div class="card-footer small text-muted">
    <span id="ultimo-movimento-pedido"></span>
  </div>
</div>

<div class="row">
  <div class="col-lg-8">
    <div class="card mb-3">
      <div class="card-header">
        <i class="fas fa-chart-bar"></i>
        Top 10 Saldo Devedor</div>
      <div class="card-body">
        <canvas id="myBarChart" width="100%" height="50"></canvas>
      </div>
      <div class="card-footer small text-muted">
        <span id="ultimo-movimento-devedor"></span>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card mb-3">
      <div class="card-header">
        <i class="fas fa-chart-pie"></i>
        Top 5 Produtos Mais Vendidos</div>
      <div class="card-body">
        <canvas id="myPieChart" width="100%" height="100"></canvas>
      </div>
      <div class="card-footer small text-muted">
        <span id="ultimo-movimento"></span>
      </div>
    </div>
  </div>
</div>

<script src="js/demo/datatables-demo.js"></script>

<?php
  $consultaPedidos = "
    SELECT
      tb_pedidos.data_pedido,
      count(tb_pedidos.id_pedido) as qtd_pedido
    FROM
      tb_pedidos
    GROUP BY
      tb_pedidos.data_pedido  
  ";
  //echo $consultaPedidos;exit;
  $queryPedidos = mysqli_query($conexao,$consultaPedidos);
  $retornoPedidos = mysqli_num_rows($queryPedidos);
  
  if($retornoPedidos > 0){
    while($objetoPedidos = mysqli_fetch_object($queryPedidos)) {
      $datas[] = $objetoPedidos->data_pedido;
      $qtdPedidos[] = $objetoPedidos->qtd_pedido;
    }
  }
?>

<!-- Pedidos -->
<script>
// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#292b2c';

// Area Chart Example
var ctx = document.getElementById("myAreaChart");
var myLineChart = new Chart(ctx, {
  type: 'line',
  data: {
    labels: [
      <?php           
        foreach($datas as $data) {
          echo "'".FormataDataInvertida($data)."',";
        }
      ?>
    ],
    datasets: [{
      label: "Pedidos",
      lineTension: 0.3,
      backgroundColor: "rgba(69, 214, 105, 1)",
      borderColor: "rgba(0, 123, 255, 1)",
      pointRadius: 5,
      pointBackgroundColor: "rgba(0, 123, 255, 1)",
      pointBorderColor: "rgba(0, 123, 255, 1)",
      pointHoverRadius: 5,
      pointHoverBackgroundColor: "rgba(2,117,216,1)",
      pointHitRadius: 50,
      pointBorderWidth: 2,
      data: [
        <?php           
        foreach($qtdPedidos as $qtdPedido) {
          echo "'".$qtdPedido."',";
        }
      ?>        
      ],
    }],
  },
  options: {
    scales: {
      xAxes: [{
        time: {
          unit: 'date'
        },
        gridLines: {
          display: false
        },
        ticks: {
          maxTicksLimit: 7
        }
      }],
      yAxes: [{
        ticks: {
          min: 0,
          max: 50,
          maxTicksLimit: 5
        },
        gridLines: {
          color: "rgba(0, 0, 0, .125)",
        }
      }],
    },
    legend: {
      display: false
    }
  }
});
document.getElementById("ultimo-movimento-pedido").innerHTML = 'Último pedido em: <?php echo $dataUltimoPedido; ?>';  
</script>
<?php
  $ultimoPagamentoGeral = "
    SELECT
      coalesce(max(tb_pagamentos.data_pagamento),'0000-00-00') as data_pagamento
    FROM
      tb_pagamentos
  ";
  $queryUltimoPagamento = mysqli_query($conexao,$ultimoPagamentoGeral);
  $objetoUltimoPagamento = mysqli_fetch_object($queryUltimoPagamento);
  $dataUltimoPagamento = $objetoUltimoPagamento->data_pagamento;

  // checa qual data irá exibir como último movimento
  ($dataUltimoPedido < $dataUltimoPagamento) ? $ultimoMovimentoDevedor = $dataUltimoPagamento : $ultimoMovimentoDevedor = $dataUltimoPedido;
  
  $consultaTop5Devedores = "
    SELECT DISTINCT 
      tb_pessoas.nome_pessoa,
      (SELECT total_pagamento(tb_pessoas.id_pessoa)) - (SELECT total_pedido(tb_pessoas.id_pessoa)) as saldo 
    FROM 
      tb_pessoas 
    HAVING
      (SELECT total_pagamento(tb_pessoas.id_pessoa)) - (SELECT total_pedido(tb_pessoas.id_pessoa)) < 0 
    ORDER BY
     (SELECT total_pagamento(tb_pessoas.id_pessoa)) - (SELECT total_pedido(tb_pessoas.id_pessoa))
    LIMIT 5  
  ";

  //echo $consultaTop5Devedores;exit;
  $queryTop5Devedores = mysqli_query($conexao,$consultaTop5Devedores);
  $retornoTop5 = mysqli_num_rows($queryTop5Devedores);
  
  if($retornoTop5 > 0){
    while($objetoTop5 = mysqli_fetch_object($queryTop5Devedores)) {
      $pessoas[] = $objetoTop5->nome_pessoa;
      $saldos[] = $objetoTop5->saldo;
    }
  }

?>

<!-- Top 5 Devedores -->
<script>
// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#292b2c';

// Bar Chart Example
var ctx = document.getElementById("myBarChart");
var myLineChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: [
      <?php           
        foreach($pessoas as $pessoa) {
          echo "'".$pessoa."',";
        }
      ?>
    ],
    datasets: [{
      label: "Saldo Devedor",
      backgroundColor: "rgba(69, 214, 105, 1)",
      borderColor: "rgba(69, 214, 105, 1)",
      data: [
        <?php           
          foreach($saldos as $saldo) {
            echo "'".abs($saldo)."',";
          }
        ?>        
      ],
    }],
  },
  options: {
    scales: {
      xAxes: [{
        time: {
          unit: 'month'
        },
        gridLines: {
          display: false
        },
        ticks: {
          maxTicksLimit: 6
        }
      }],
      yAxes: [{
        ticks: {
          min: 0,
          max: 200,
          maxTicksLimit: 5
        },
        gridLines: {
          display: true
        }
      }],
    },
    legend: {
      display: false
    }
  }
});
document.getElementById("ultimo-movimento-devedor").innerHTML = 'Último pedido em <?php echo $dataUltimoPedido; ?>';

</script>

<?php
  $consultaTop5Produtos = "
  SELECT DISTINCT 
	  tb_produtos.nome_produto, 
    max(tb_movimentacao.data_movimentacao) as ultimo_movimento,
    count(tb_movimentacao.id_produto) as qtd_produto 
  FROM 
    tb_movimentacao 
    INNER JOIN tb_produtos on tb_produtos.id_produto = tb_movimentacao.id_produto 
  GROUP BY
    tb_produtos.nome_produto
  ORDER BY 
    count(tb_movimentacao.id_produto) DESC
  LIMIT 5    
  ";

  //echo $consultaTop5Produtos;exit;
  $queryTop5Produtos = mysqli_query($conexao,$consultaTop5Produtos);
  $retornoTop5 = mysqli_num_rows($queryTop5Produtos);
  
  if($retornoTop5 > 0){
    while($objetoTop5 = mysqli_fetch_object($queryTop5Produtos)) {
      $produtos[] = $objetoTop5->nome_produto;
      $quantidades[] = $objetoTop5->qtd_produto;
      $ultimoMovimentoTop5 = $objetoTop5->ultimo_movimento;
    }
  } 
?>

<!-- Top 5 Produtos Mais Vendidos -->
<script>
  // Set new default font family and font color to mimic Bootstrap's default styling
  Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
  Chart.defaults.global.defaultFontColor = '#292b2c';

  // Pie Chart Example
  var ctx = document.getElementById("myPieChart");
  var myPieChart = new Chart(ctx, {
    type: 'pie',
    data: {
      labels: [
        <?php           
          foreach($produtos as $produto) {
            echo "'".$produto."',";
          }
        ?>
      ],
      datasets: [{
        data: [
          <?php           
            foreach($quantidades as $quantidade) {
              echo "'".$quantidade."',";
            }
          ?>
        ],
        backgroundColor: ['#007bff', '#dc3545', '#ffc107', '#28a745', '#999999'],
      }],
    },
  });
  document.getElementById("ultimo-movimento").innerHTML = 'Último pedido em <?php echo $dataUltimoPedido; ?>';
</script>