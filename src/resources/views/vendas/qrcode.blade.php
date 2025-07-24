

<input type="text" id="qrcode-scanner" class="opacity-0 absolute -left-full">

<script>
// resources/js/app.js
$(document).ready(function() {
    const $qrInput = $('#qrcode-scanner');
    let lastScan = Date.now();

    $qrInput.on('input', function(e) {
        // Debounce para evitar leituras múltiplas
        if (Date.now() - lastScan < 500) return;
        
        const codigo = $(this).val().trim();
        if (codigo.length > 0) {
            registrarVenda(codigo);
            $(this).val('');
            lastScan = Date.now();
        }
    });

    function registrarVenda(codigo) {
        $.ajax({
            url: '/api/registrar-venda',
            method: 'POST',
            data: { codigo: codigo },
            success: function() {
                // Feedback opcional
            },
            error: function(xhr) {
                console.error('Erro:', xhr.responseJSON?.message);
            }
        });
    }
});
</script>