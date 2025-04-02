@extends('layouts.principal')

@section('conteudo')
<div class="bg-white p-6 rounded-md shadow-md w-full max-w-7xl mx-auto">

    <!-- Scanner de QR Code -->
    <h1 class="text-2xl md:text-3xl font-bold mb-6 text-center">Registrar Venda</h1>

    <div class="flex flex-col items-center md:flex-row md:justify-center gap-4">
        <button id="start-button" class="bg-green-500 text-white px-6 py-2 rounded-lg text-lg w-full md:w-auto">
            Iniciar Câmera
        </button>

        <div id="qr-reader" class="w-full max-w-sm md:max-w-md h-72 mt-4 hidden mx-auto"></div>

        <button id="stop-button" class="mt-4 bg-red-500 text-white px-6 py-2 rounded-lg text-lg hidden w-full md:w-auto">
            Parar Câmera
        </button>
    </div>

    <div id="output" class="mt-4 hidden text-center">
        <p class="text-lg font-semibold">QR Code Escaneado:</p>
        <pre id="result" class="bg-gray-200 p-3 rounded text-lg break-words"></pre>
    </div>

    <!-- Tabela de Produtos Vendidos -->
    <h2 class="text-xl md:text-2xl font-bold mt-8 mb-4 text-center">Últimos Produtos Vendidos</h2>

    <div class="overflow-x-auto">
        <table class="table-auto w-full border-collapse border border-gray-200 rounded-md shadow-sm">
            <thead class="bg-gray-100">
                <tr class="text-sm md:text-base text-gray-600">
                    <th class="py-3 px-4 md:px-6 text-left font-medium">Nome</th>
                    <th class="py-3 px-4 md:px-6 text-left font-medium">Preço Venda</th>
                    <th class="py-3 px-4 md:px-6 text-left font-medium">Cod. Produto</th>
                    <th class="py-3 px-4 md:px-6 text-left font-medium">Quantidade</th>
                    <th class="py-3 px-4 md:px-6 text-left font-medium">Vendedor</th>
                </tr>
            </thead>
            <tbody id="historico-produtos">
                @foreach ($vendas as $venda)
                <tr class="border-b border-gray-200 hover:bg-gray-50 text-sm md:text-base">
                    <td class="py-3 px-4 md:px-6 text-gray-600">{{$venda->produto->nome_produto}}</td>
                    <td class="py-3 px-4 md:px-6 text-gray-600">R$ {{number_format($venda->preco_venda, 2, ',', '.')}}</td>
                    <td class="py-3 px-4 md:px-6 text-gray-600">{{$venda->produto->cod_produto}}</td>
                    <td class="py-3 px-4 md:px-6 text-gray-600">{{$venda->quantidade}}</td>
                    <td class="py-3 px-4 md:px-6 text-gray-600">{{$venda->usuario->name}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Paginação -->
    <div class="mt-6 flex justify-center">
        <nav class="flex items-center space-x-2">
            <a href="{{$vendas->previousPageUrl()}}" class="py-2 px-3 bg-gray-100 border rounded-l-lg hover:bg-gray-200 text-gray-600">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
            </a>
            <span class="py-2 px-3 bg-gray-100 text-gray-600">{{$vendas->currentPage()}}</span>
            <a href="{{$vendas->nextPageUrl()}}" class="py-2 px-3 bg-gray-100 border rounded-r-lg hover:bg-gray-200 text-gray-600">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
            </a>
        </nav>
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