
$(document).ready(function () 
{
$('.toggle-ativacao').click(function () {
var button = $(this);
var produtoId = button.data('id');
var grupo = window.location.pathname.split('/')[2];
console.log(button);
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

$(".quantidade").each(function() {
var quantidade = $(this).data('quantidade');
var tr = $(this).closest('tr');

$("#aviso").each(function() {
var aviso = $(this).data('aviso');
if (quantidade <= aviso) {
    console.log('quantidade',quantidade, 'aviso' ,aviso)
    tr.find('td').css("background-color", "red");
}
});

});

//Produto
//Index

$(document).ready(function () 
{
    var today = new Date();
    $(".expiration-date").each(function () {
      var data = $(this).text();
      var dataFormatada = moment(data).format('DD/MM/YYYY');
      $(this).text(dataFormatada); 
  
      var expirationDate = new Date(data);
      var vencimento = Math.floor((expirationDate - today) / (24 * 60 * 60 * 1000));
  
      if (vencimento < 0) {
        $(this).closest('tr').find('td').css("background-color", "red");
      } else if (vencimento <= 7) {
        $(this).closest('tr').find('td').css("background-color", "yellow");
      }
    });
    
});  

//Fornecedor
//Cadastro e Editar

$("#cep").blur(function()
{
    var cep = this.value.replace(/[^0-9]/g, "");
    if (cep.length !== 8) {
        this.style.backgroundColor = "red"; 
    } else {
        this.style.backgroundColor = ""; 
    }
    var url = "https://viacep.com.br/ws/"+cep+"/json/";
    $.getJSON(url, function(dadosRetorno){
        try{
            $("#endereco").val(dadosRetorno.logradouro);
            $("#bairro").val(dadosRetorno.bairro);
            $("#cidade").val(dadosRetorno.localidade);
            $("#uf").val(dadosRetorno.uf);
        }catch(ex){
            console.log(ex);
        }
    });
});

$("#cnpj").blur(function() {
    var cnpj = this.value.replace(/[^0-9]/g, "");
    if (cnpj.length !== 14) {
        this.style.backgroundColor = "red"; 
    } else {
        this.style.backgroundColor = ""; 
    }
});
});



