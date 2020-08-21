<link href="consultas/css/consultas.css" rel="stylesheet">
<script src="consultas/js/consultas.js"></script>

<?php
$consultaGeral = "
SELECT DISTINCT
    tb_pessoas.id_pessoa, 
    tb_pessoas.nome_pessoa, 
    (SELECT ultima_compra(tb_pessoas.id_pessoa)) as ultima_compra,
    (SELECT ultimo_pagamento(tb_pessoas.id_pessoa)) as ultimo_pagamento,
    (SELECT total_pedido(tb_pessoas.id_pessoa)) as valor_pedido,    
    (SELECT total_pagamento(tb_pessoas.id_pessoa)) as valor_pago,
    (SELECT total_pagamento(tb_pessoas.id_pessoa)) - (SELECT total_pedido(tb_pessoas.id_pessoa)) as saldo 
FROM 
    tb_pessoas
";

$queryConsulta = mysqli_query($conexao,$consultaGeral);
?>


<legend>Área de Consultas</legend>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title> Bootstrap </title>
        <!--
          <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.js">
          <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
        -->
    </head>
    <body>

        <div class = "container mb-3 mt-3">
            <table class="table table-striped table-bordered mydatatable" style="width: 100%" id="mydatatable">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Última Compra</th>
                        <th>Último Pagamento</th>
                        <th>Total Pedido</th>
                        <th>Total Pago</th>
                        <th>Saldo Final</th>
                    </tr>
                
                </thead>
                <tbody>
                    <?php                    
                        while($objetoConsulta = mysqli_fetch_object($queryConsulta)) {
                            echo "<tr>";
                                echo "<td><a title='".$objetoConsulta->id_pessoa."' id='".$objetoConsulta->saldo."' class='detalhe-pedido' data-toggle='modal' data-target='#pedidoModal' href='#'>".$objetoConsulta->nome_pessoa."</a></td>";
                                echo "<td>".FormataDataInvertida($objetoConsulta->ultima_compra)."</td>";
                                echo "<td>".FormataDataInvertida($objetoConsulta->ultimo_pagamento)."</td>";
                                echo "<td>R$ ".FormataValorBR($objetoConsulta->valor_pedido)."</td>";
                                echo "<td>R$ ".FormataValorBR($objetoConsulta->valor_pago)."</td>";
                                if($objetoConsulta->saldo < 0) {
                                    $cor = "red";
                                }
                                else {
                                    $cor = "blue";
                                }
                                echo "<td id='valor_saldo' style='color: ".$cor."'>R$ ".FormataValorBR($objetoConsulta->saldo)."</td>";
                            echo "</tr>";
                        }    
                    ?>                
                </tbody>
                <tfoot>
                    <tr>
                        <th>Nome</th>
                        <th>Última Compra</th>
                        <th>Último Pagamento</th>
                        <th>Total Pedido</th>
                        <th>Total Pago</th>
                        <th>Saldo Final</th>
                    </tr>
                
                </tfoot>  

            </table>
    
        </div>
        <!--
          <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
          <script src="https://cdnjs.cloudflare.com/ajax/libs/pooper.js/1.14.7/umd/popper.min.js"></script>
          <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
        -->

        <script src="./DataTables/jquery.dataTables.min.js"></script>
        <script src="./DataTables/dataTables.bootstrap4.min.js"></script>
        <script>
            $('.mydatatable').dataTable();
        </script>
        <div id="modal-container">
          <!-- Modal -->
          <div class="modal fade" id="pedidoModal" tabindex="-1" role="dialog" aria-labelledby="pedidoModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content pedido-modal">
                <div class="modal-header">
                  <h5 class="modal-title" id="pedidoModalLabel">Pedidos - Detalhes</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <input type="checkbox" id="exibePagamento" value="">Exibir Pagamentos
                  <div id="modal-pedidos"><!-- retorno do Post do Jquery (./consultas/consultas.js) --></div>
                </div>
                <div class="modal-footer">
                  <button id="cancelar-pedido" type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                </div>
              </div>
            </div>
          </div>
        </div>        
    </body>
</html>
