<button
    data-id="{{ $modelId }}"
    class="toggle-status px-3 py-1 rounded-full text-sm transition-colors 
           {{ $status ? 'bg-green-200 text-green-700 hover:bg-green-300' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
    {{ $status ? 'Ativo' : 'Inativo' }}
</button>

<script>
document.querySelectorAll('.toggle-status').forEach(button => {
    button.addEventListener('click', function() {
        const modelId = this.getAttribute('data-id');
        const modelName = "{{ $modelName }}";
        
        fetch(`/status/${modelName}/${modelId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 200) {
                this.classList.toggle('bg-green-200');
                this.classList.toggle('text-green-700');
                this.classList.toggle('bg-gray-200');
                this.classList.toggle('text-gray-700');
                this.textContent = data.new_status ? 'Ativo' : 'Inativo';
            }
        });
    });
});
</script>