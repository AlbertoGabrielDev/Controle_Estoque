@extends('layouts.principal')

@section('conteudo')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8 text-gray-800">Processar Vídeo</h1>

    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
        <!-- Formulário de Upload -->
        <form id="uploadForm" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="video">
                    Selecione um vídeo MP4
                </label>
                <input type="file" name="video" id="videoInput" 
                       class="block w-full text-sm text-gray-500
                              file:mr-4 file:py-2 file:px-4
                              file:rounded-full file:border-0
                              file:text-sm file:font-semibold
                              file:bg-blue-50 file:text-blue-700
                              hover:file:bg-blue-100"
                       accept="video/mp4">
            </div>

            <!-- Pré-visualização do Vídeo -->
            <div id="videoPreview" class="mb-4 hidden">
                <video controls class="w-full rounded-lg" id="previewPlayer">
                    Seu navegador não suporta a tag de vídeo.
                </video>
            </div>

            <!-- Status do Upload -->
            <div id="uploadStatus" class="hidden mb-4 p-4 rounded-lg bg-blue-50 text-blue-800">
                <div class="flex items-center">
                    <div class="animate-spin mr-2">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    <span>Processando vídeo...</span>
                </div>
            </div>

            <!-- Mensagens de Feedback -->
            <div id="messageContainer" class="hidden p-4 rounded-lg mb-4"></div>

            <button type="submit" 
                    id="submitBtn"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                Processar Vídeo
            </button>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    const videoInput = $('#videoInput');
    const previewPlayer = $('#previewPlayer')[0];
    const videoPreview = $('#videoPreview');
    const uploadStatus = $('#uploadStatus');
    const messageContainer = $('#messageContainer');
    const submitBtn = $('#submitBtn');

    // Pré-visualização do vídeo
    videoInput.change(function(e) {
        const file = e.target.files[0];
        if (file) {
            const url = URL.createObjectURL(file);
            previewPlayer.src = url;
            videoPreview.removeClass('hidden');
        }
    });

    // Envio do formulário
    $('#uploadForm').submit(function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        submitBtn.prop('disabled', true);
        uploadStatus.removeClass('hidden');
        messageContainer.addClass('hidden');

        $.ajax({
            url: "{{ route('video.upload') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                showMessage('success', response.success);
                videoPreview.addClass('hidden');
                $('#uploadForm')[0].reset();
            },
            error: function(xhr) {
                const error = xhr.responseJSON?.error || 'Erro desconhecido';
                showMessage('error', error);
            },
            complete: function() {
                submitBtn.prop('disabled', false);
                uploadStatus.addClass('hidden');
            }
        });
    });

    function showMessage(type, text) {
        messageContainer.removeClass('hidden bg-red-50 text-red-800 bg-green-50 text-green-800')
                       .addClass(type === 'error' ? 'bg-red-50 text-red-800' : 'bg-green-50 text-green-800')
                       .text(text);
    }
});
</script>
@endsection