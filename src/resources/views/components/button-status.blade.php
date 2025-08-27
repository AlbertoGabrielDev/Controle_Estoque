@if(auth()->check() && auth()->user()->canToggleStatus())
<button
    data-id="{{ $modelId }}"
    data-url="{{ route($modelName . '.status', ['modelName' => $modelName, 'id' => $modelId]) }}"
    class="toggle-status flex items-center px-3 py-1 rounded-full text-sm transition-colors gap-1 
           {{ $status ? 'bg-green-400 hover:bg-green-400' : 'bg-red-400 hover:bg-red-400' }}"
    data-status="{{ $status ? 1 : 0 }}"
    data-processing="false"
    title="{{ $status ? 'Desativar' : 'Ativar' }}">
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

            button.classList.remove('bg-green-400','hover:bg-green-400','bg-red-400','hover:bg-red-400');
            if (isActive) button.classList.add('bg-green-400','hover:bg-green-400');
            else button.classList.add('bg-red-400','hover:bg-red-400');

            button.dataset.status = isActive ? '1' : '0';

            const message = isActive ? 'Status ativado com sucesso!' : 'Status desativado com sucesso!';
            showToast(message, isActive ? 'success' : 'error');
        } else {
            showToast(data.message || 'Erro ao atualizar status.', 'error');
        }
    })
    .catch(console.error)
    .finally(() => { button.dataset.processing = 'false'; });
});

function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container') || (() => {
        const d = document.createElement('div');
        d.id = 'toast-container';
        d.className = 'fixed top-4 right-4 z-50 flex flex-col gap-2';
        document.body.appendChild(d);
        return d;
    })();
    const toast = document.createElement('div');
    toast.className = `flex items-center w-full max-w-xs p-4 rounded-lg shadow-sm text-sm fade-in gap-3
        ${type === 'success' ? 'bg-green-400 text-white' : ''}
        ${type === 'error' ? 'bg-red-400 text-white' : ''}`;
    toast.innerHTML = `<span class="flex-1">${message}</span>
        <button type="button" class="ml-2 text-white/80 hover:text-white" onclick="this.closest('.toast').remove()">✕</button>`;
    toast.classList.add('toast');
    container.appendChild(toast);
    setTimeout(() => { toast.classList.add('fade-out'); setTimeout(() => toast.remove(), 500); }, 3000);
}
</script>

<style>
.fade-in { opacity: 0; animation: fadeIn .3s forwards; }
.fade-out { animation: fadeOut .5s forwards; }
@keyframes fadeIn { to { opacity: 1 } }
@keyframes fadeOut { to { opacity: 0 } }
</style>
@endif
