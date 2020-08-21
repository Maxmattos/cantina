$(document).ready(function() {
  $(".detalhe-pedido").click(function(){
    $.post("./consultas/detalhes_pedido.php", { 
        //envia POST para PHP
        cliente: $(this).attr("title"), 
        total: $(this).attr("id")
      })
    .done(function( data ) {
      $("#modal-pedidos").html(data);
    });
  });
});