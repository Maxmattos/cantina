$(document).ready(function() {
    $('#InputProdutos').multiSelect();

    $("#btnPedir").click(function(){
      console.log($("#idCliente").val());
      if($("#InputProdutos").val() == "" || $("#idCliente").val() == "selecione") {
        $("#finalizar-pedido").hide();
      } else {        
        $("#finalizar-pedido").show();
      }
      $.post("./pedidos/carrinho.php", { 
          data: $("#data").val(),
          cliente: $("#idCliente").val(),
          produtos: $("#InputProdutos").val(),          
        })
      .done(function( data ) {
        $("#modal-carrinho").html(data);
      });
    });
});