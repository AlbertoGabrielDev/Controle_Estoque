@extends('layouts.principal')

@section('conteudo')
<div class="min-h-screen bg-gray-100 p-6">

    <!-- Scanner de QR Code -->
    <div class="mx-auto bg-white p-6 rounded-lg shadow-lg w-full">
        <h1 class="text-2xl font-bold mb-4">Registrar Venda</h1>

        <button id="start-button" class="bg-green-500 text-white px-6 py-2 rounded-lg text-lg">Iniciar Câmera</button>
        <div id="qr-reader" class="w-full max-w-xl h-72 mt-4 hidden mx-auto"></div>
        <button id="stop-button" class="mt-4 bg-red-500 text-white px-6 py-2 rounded-lg text-lg hidden">Parar Câmera</button>

        <div id="output" class="mt-4 hidden">
            <p class="text-lg font-semibold">QR Code Escaneado:</p>
            <pre id="result" class="bg-gray-200 p-3 rounded text-lg"></pre>
        </div>
    </div>

    <!-- Tabela de produtos retirados -->
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-5xl mt-6">
        <h2 class="text-xl font-bold mb-4 text-center">Últimos 10 Produtos Retirados</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border border-gray-300 p-3">Nome</th>
                        <th class="border border-gray-300 p-3">Preço Venda</th>
                        <th class="border border-gray-300 p-3">Cod. Produto</th>
                        <th class="border border-gray-300 p-3">Quantidade</th>
                        <th class="border border-gray-300 p-3">Vendedor</th>
                    </tr>
                </thead>
                <tbody id="historico-produtos">
                 @foreach ($vendas as $venda) 
                 <tr class="border-b border-gray-200 hover:bg-gray-50">
                     <td class="border border-gray-300 p-3">{{$venda->produto->nome_produto}}</td>
                     <td class="border border-gray-300 p-3">R$ {{number_format($venda->preco_venda, 2, ',', '.')}}</td>
                     <td class="border border-gray-300 p-3">{{$venda->produto->cod_produto}}</td>
                     <td class="border border-gray-300 p-3">{{$venda->quantidade}}</td>
                     <td class="border border-gray-300 p-3">{{$venda->usuario->name}}</td>
                 </tr>
                 @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.4/html5-qrcode.min.js"></script>
<script>
    let html5QrCode;

    function startQRCodeScanner() {
        html5QrCode = new Html5Qrcode("qr-reader");
        const config = {
            fps: 10,
            qrbox: {
                width: 300,
                height: 300
            }
        };

        html5QrCode.start({
                facingMode: "environment"
            },
            config,
            (decodedText) => {
                document.getElementById('result').textContent = decodedText;
                $('#output').removeClass('hidden');
                sendQRCodeToBackend(decodedText);
                stopQRCodeScanner();
            },
            (errorMessage) => {
                console.log(errorMessage);
            }
        ).catch((err) => {
            console.log("Erro ao iniciar o scanner: ", err);
        });

        $('#qr-reader').removeClass('hidden');
        $('#stop-button').removeClass('hidden');
        $('#start-button').addClass('hidden');
    }

    function stopQRCodeScanner() {
        if (html5QrCode) {
            html5QrCode.stop().then(() => {
                $('#qr-reader').addClass('hidden');
                $('#start-button').removeClass('hidden');
                $('#stop-button').addClass('hidden');
            }).catch((err) => {
                console.log("Erro ao parar a câmera: ", err);
            });
        }
    }

    function sendQRCodeToBackend(qrCode) {
        $.ajax({
            url: "vendas/registrar-venda",
            method: "POST",
            data: {
                codigo_qr: qrCode,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                toastr.success("Venda registrada com sucesso!", "Sucesso");
                addProductToTable(response.produto);
            },
            error: function(xhr, status, error) {
                toastr.error("Erro ao registrar a venda!", "Erro");
                console.error("Erro ao enviar QR Code:", error);
            }
        });
    }

    $('#start-button').on('click', startQRCodeScanner);
    $('#stop-button').on('click', stopQRCodeScanner);
</script>
@endsection