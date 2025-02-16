<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processamento de Vídeos</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-3xl mx-auto bg-white p-6 rounded-2xl shadow-lg">
        <h1 class="text-2xl font-bold mb-4">Processar Vídeos</h1>
        @if (session('success'))
            <div class="bg-green-200 text-green-800 p-4 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif
        <form action="videos/upload" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="file" name="video" class="block w-full p-2 border rounded mb-4">
            <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded">Enviar e Processar</button>
        </form>
    </div>
</body>
</html>
