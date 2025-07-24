<button
    data-id="{{ $modelId }}"
    data-url="{{ route($modelName . '.status', ['modelName' => $modelName, 'id' => $modelId]) }}"
    class="toggle-status flex items-center px-3 py-1 rounded-full text-sm transition-colors gap-1 
           {{ $status ? 'bg-green-400 hover:bg-green-400' : 'bg-red-400 hover:bg-red-400' }}"
    data-status="{{ $status ? 1 : 0 }}"
    data-processing="false">
    <i class="fa-solid fa-power-off text-xs text-gray-600"></i>
</button>

<script>
    document.addEventListener('click', function(e) {
        const button = e.target.closest('.toggle-status');
        if (!button || button.dataset.processing === 'true') return;

        button.dataset.processing = 'true';
        const url = button.getAttribute('data-url');

        fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 200) {
                    const isActive = data.new_status;

                    // Atualiza classe do botão
                    button.classList.remove('bg-green-400', 'hover:bg-green-400', 'bg-red-400', 'hover:bg-red-400');
                    if (isActive) {
                        button.classList.add('bg-green-400', 'hover:bg-green-400');
                    } else {
                        button.classList.add('bg-red-400', 'hover:bg-red-400');
                    }

                    // Atualiza status
                    button.dataset.status = isActive ? '1' : '0';

                    // Toast com mensagem e cor específica
                    const message = isActive ? 'Status ativado com sucesso!' : 'Status desativado com sucesso!';
                    const type = isActive ? 'success' : 'error';
                    showToast(message, type);
                } else {
                    showToast(data.message || 'Erro ao atualizar status.', 'error');
                }
            })
            .catch(console.error)
            .finally(() => {
                button.dataset.processing = 'false';
            });
    });

    function showToast(message, type = 'success') {
        const toastContainer = document.getElementById('toast-container');

        const toast = document.createElement('div');
        toast.className = `flex items-center w-full max-w-xs p-4 rounded-lg shadow-sm text-sm fade-in gap-3
                            ${type === 'success' ? 'bg-green-400 text-white' : ''}
                            ${type === 'error' ? 'bg-red-400 text-white' : ''}
                            ${type === 'warning' ? 'bg-yellow-400 text-black' : ''}`;

        const iconPath = {
            success: 'M10 .5a9.5...', // substitua com o path correto
            error: 'M10 .5a9.5...', // substitua com o path correto
            warning: 'M10 .5a9.5...', // substitua com o path correto
        } [type] || 'M15.147...'; // fallback

        const iconColor = {
            success: 'text-white bg-green-600',
            error: 'text-white bg-red-600',
            warning: 'text-black bg-yellow-500'
        } [type] || 'text-blue-500 bg-blue-500';

        toast.innerHTML = `
            <div class="inline-flex items-center justify-center shrink-0 w-8 h-8 rounded-lg ${iconColor}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 18 20" xmlns="http://www.w3.org/2000/svg">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="${iconPath}" />
                </svg>
            </div>
            <span class="flex-1">${message}</span>
            <button type="button" class="ml-2 text-gray-400 hover:text-gray-700 focus:outline-none"
                onclick="this.closest('.toast').remove()">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14" xmlns="http://www.w3.org/2000/svg">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M1 1l12 12M13 1L1 13" />
                </svg>
            </button>
        `;
        toast.classList.add('toast');

        toastContainer.appendChild(toast);

        // Remove depois de 3 segundos
        setTimeout(() => {
            toast.classList.add('fade-out');
            setTimeout(() => toast.remove(), 500);
        }, 3000);
    }
</script>
<style>
    .material-icons-outlined {
        font-family: 'Material Symbols Outlined';
        font-weight: normal;
        font-style: normal;
        font-size: 1.1rem;
        line-height: 1;
        letter-spacing: normal;
        text-transform: none;
        display: inline-block;
        white-space: nowrap;
        word-wrap: normal;
        direction: ltr;
        -webkit-font-feature-settings: 'liga';
        -webkit-font-smoothing: antialiased;
    }


    .fade-in {
        opacity: 0;
        animation: fadeIn 0.3s forwards;
    }

    .fade-out {
        animation: fadeOut 0.5s forwards;
    }

    @keyframes fadeIn {
        to {
            opacity: 1;
        }
    }

    @keyframes fadeOut {
        to {
            opacity: 0;
        }
    }
</style>