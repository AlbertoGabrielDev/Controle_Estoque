@extends('layouts.principal')

@section('conteudo')
<h1>Registrar Venda</h1>

<button id="start-button">Iniciar Câmera</button>
<div id="qr-reader" style="width: 100%; height: 400px; display: none;"></div>
<button id="stop-button" style="display: none;">Parar Câmera</button>

<div id="output" style="margin-top: 20px;">
    <p>QR Code Escaneado:</p>
    <pre id="result"></pre>
</div>

<!-- Corrigindo o link para a biblioteca html5-qrcode -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.4/html5-qrcode.min.js"></script>

<script>
    let html5QrCode; // Variável global para controlar a instância

    function startQRCodeScanner() {
        html5QrCode = new Html5Qrcode("qr-reader");
        const config = { fps: 10, qrbox: 250 };

        html5QrCode.start(
            { facingMode: "environment" },
            config,
            (decodedText) => {
                document.getElementById('result').textContent = decodedText;
                console.log(decodedText,'fff');
                sendQRCodeToBackend(decodedText);
                stopQRCodeScanner();
            },
            (errorMessage) => {
                console.log(errorMessage);
            }
        ).catch((err) => {
            console.log("Erro ao iniciar o scanner: ", err);
        });

        document.getElementById('qr-reader').style.display = 'block';
        document.getElementById('stop-button').style.display = 'block';
        document.getElementById('start-button').style.display = 'none';
    }

    function stopQRCodeScanner() {
        if (html5QrCode) {
            html5QrCode.stop().then(() => {
                document.getElementById('qr-reader').style.display = 'none';
                document.getElementById('start-button').style.display = 'block';
                document.getElementById('stop-button').style.display = 'none';
            }).catch((err) => {
                console.log("Erro ao parar a câmera: ", err);
            });
        }
    }

    function sendQRCodeToBackend(qrCode) {
        $.ajax({
            url: "vendas/registrar-venda", // Rota definida no Laravel
            method: "POST",
            data: {
                codigo_qr: qrCode,
                _token: "{{ csrf_token() }}" // Importante para segurança
            },
            success: function(response) {
                alert(response.message); // Exibe mensagem de sucesso
            },
            error: function(xhr, status, error) {
                console.error("Erro ao enviar QR Code:", error);
            }
        });
    }

    // ... (restante do código permanece igual)
    
    document.getElementById('start-button').addEventListener('click', startQRCodeScanner);
    document.getElementById('stop-button').addEventListener('click', stopQRCodeScanner);
</script>
@endsection