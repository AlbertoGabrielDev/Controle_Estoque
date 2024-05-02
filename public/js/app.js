
//Método para mudar o status de inativo para ativo, das tabelas fornecedor,produtos,categoria, estoque,usuario,marca
$(document).ready(function () 
{
    $('.toggle-ativacao').click(function () {
        var button = $(this);
        var produtoId = button.data('id');
        var grupo = window.location.pathname.split('/')[2];
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            url: '/verdurao/'+grupo+'/status/' + produtoId,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function (response) 
            {
            if (response.status === 1) 
            {
                button.text('Inativar').removeClass('btn-success').addClass('btn-danger');
            } else {
                button.text('Ativar').removeClass('btn-danger').addClass('btn-success');
            }
            },
            error: function () {
                console.log(error);
            }
        });
    });

//Método para informar se a quantidade de produto de estoque e menor que a que o usuario desejou ser informado
$(".quantidade").each(function() {
    var quantidade = parseInt($(this).data('quantidade'));
    var tr = $(this).closest('tr');
    var aviso = parseInt(tr.find('.aviso').data('aviso'));
    console.log('quantidade', quantidade);
    console.log('aviso', aviso);
    if (quantidade <= aviso) {
        tr.find('td').css("background-color", "#FF6347");
    }
});

//Método para informar se o produto já validou ou se está prestes a vencer
    var today = new Date();
    $(".expiration-date").each(function () {
      var data = $(this).text();
      var dataFormatada = moment(data).format('DD/MM/YYYY');
      $(this).text(dataFormatada); 
      var expirationDate = new Date(data);
      var vencimento = Math.floor((expirationDate - today) / (24 * 60 * 60 * 1000));
    //   if (vencimento < 0) {
    //     $(this).closest('tr').find('td').css("background-color", "red");
    //   } else if (vencimento <= 7) {
    //     $(this).closest('tr').find('td').css("background-color", "yellow");
    //   }
    });

//Método para fornecedor o endereço do fornecedor baseado no CEP    
    $("#cep").blur(function(event)
    {
        event.preventDefault(); 
        var cep = this.value.replace(/[^0-9]/, "");
        var url = "https://viacep.com.br/ws/"+cep+"/json/";
    $.getJSON(url, function(dadosRetorno){
        console.log(dadosRetorno)
        try{
           if (!dadosRetorno.erro) {
            $("#endereco").val(dadosRetorno.logradouro);
            $("#bairro").val(dadosRetorno.bairro);
            $("#cidade").val(dadosRetorno.localidade);
            $("#uf").val(dadosRetorno.uf);

            $("#error-message").text("");
            $("#error-message").hide();
           } else{
            $("#error-message").text("CEP inválido. O CEP não existe.");
            $("#error-message").show();
           }
        }catch(ex){
            console.log(ex);
        }
    });
    });


//Replace para virgula em ponto      
      $('input[name="preco_custo"], input[name="preco_venda"]').on('input', function() {
        $(this).val($(this).val().replace(/[^\d.,]/g, '').replace(',', '.'));
      });

//Mascara para cep e cnpj
   
    $(document).ready(function() {
        $('#cnpj_td').text(function(index, text) {
            return text.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/, '$1.$2.$3/$4-$5');
        });
    });

    $(document).ready(function() {
        $('#cep_td').text(function(index, text) {
            return text.replace(/^(\d{5})(\d{3})$/, '$1-$2');
        });
    });

});





