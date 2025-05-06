
//Método para mudar o status de inativo para ativo, das tabelas fornecedor,produtos,categoria, estoque,usuario,marca
$(document).ready(function () {
    $('.toggle-ativacao').click(function () {
        var button = $(this);
        var produtoId = button.data('id');
        var grupo = window.location.pathname.split('/')[2];
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            url: '/verdurao/' + grupo + '/status/' + produtoId,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function (response) {
                var icon = button.find('i');

                if (response.status === 1) {
                    // Produto ativo — mostra botão para inativar (ícone vermelho)
                    icon.removeClass('text-green-600').addClass('text-red-600');
                } else {
                    // Produto inativo — mostra botão para ativar (ícone verde)
                    icon.removeClass('text-red-600').addClass('text-green-600');
                }

                if (response.message) {
                    showToast(response.message, response.type || 'success');
                }
            },
            error: function () {
                console.log(error);
            }
        });
    });
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast-default');
        const toastMessage = document.getElementById('toast-message');
        const toastIcon = document.getElementById('toast-icon');

        let iconColor = 'text-blue-500 bg-blue-100';
        let iconPath = 'M15.147...'; // ícone genérico

        // Limpa classes anteriores
        toast.className = "flex items-center w-full max-w-xs p-4 rounded-lg shadow-sm fixed top-4 right-4 z-50 opacity-0";

        if (type === 'success') {
            iconColor = 'text-green-500 bg-green-100';
            iconPath = 'M10 .5a9.5...';
            toast.classList.add('bg-green-100');
        } else if (type === 'error') {
            iconColor = 'text-red-500 bg-red-100';
            iconPath = 'M10 .5a9.5...';
            toast.classList.add('bg-red-100');
        } else if (type === 'warning') {
            iconColor = 'text-yellow-500 bg-yellow-100';
            iconPath = 'M10 .5a9.5...';
            toast.classList.add('bg-yellow-100');
        }

        toastMessage.textContent = message;

        toastIcon.innerHTML = `
            <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 20">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${iconPath}"/>
            </svg>
        `;
        toastIcon.className = `inline-flex items-center justify-center shrink-0 w-8 h-8 rounded-lg ${iconColor}`;

        toast.classList.remove('hidden');
        setTimeout(() => {
            toast.classList.remove('opacity-0');
            toast.classList.add('fade-in');
        }, 10);

        setTimeout(() => {
            toast.classList.remove('fade-in');
            toast.classList.add('fade-out');
            setTimeout(() => toast.classList.add('hidden'), 500);
        }, 5000);

        const closeButton = toast.querySelector('[data-dismiss-target="#toast-default"]');
        closeButton.onclick = () => {
            toast.classList.add('fade-out');
            setTimeout(() => toast.classList.add('hidden'), 500);
        };
    }
    //Método para informar se a quantidade de produto de estoque e menor que a que o usuario desejou ser informado
    $(".quantidade").each(function () {
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
    $("#cep").blur(function (event) {
        event.preventDefault();
        var cep = this.value.replace(/[^0-9]/, "");
        var url = "https://viacep.com.br/ws/" + cep + "/json/";
        $.getJSON(url, function (dadosRetorno) {
            console.log(dadosRetorno)
            try {
                if (!dadosRetorno.erro) {
                    $("#endereco").val(dadosRetorno.logradouro);
                    $("#bairro").val(dadosRetorno.bairro);
                    $("#cidade").val(dadosRetorno.localidade);
                    $("#uf").val(dadosRetorno.uf);

                    $("#error-message").text("");
                    $("#error-message").hide();
                } else {
                    $("#error-message").text("CEP inválido. O CEP não existe.");
                    $("#error-message").show();
                }
            } catch (ex) {
                console.log(ex);
            }
        });
    });


    //Replace para virgula em ponto      
    $('input[name="preco_custo"], input[name="preco_venda"]').on('input', function () {
        $(this).val($(this).val().replace(/[^\d.,]/g, '').replace(',', '.'));
    });

    //Mascara para cep e cnpj

    $(document).ready(function () {
        $('#cnpj_td').text(function (index, text) {
            return text.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/, '$1.$2.$3/$4-$5');
        });
    });

    $(document).ready(function () {
        $('#cep_td').text(function (index, text) {
            return text.replace(/^(\d{5})(\d{3})$/, '$1-$2');
        });
    });

});
document.addEventListener('DOMContentLoaded', function() {
    $('.select2-multiple').select2({
      placeholder: "Selecione as permissões",
      allowClear: true,
      width: '100%',
    });
  });




