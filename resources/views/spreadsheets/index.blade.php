<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comparador de Planilhas</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-5 bg-gray-100">

    <div class="mb-5 bg-white p-5 shadow rounded">
        <form id="upload-form" enctype="multipart/form-data">
            @csrf
            <input type="file" name="file1" id="file1" required class="mr-2">
            <input type="file" name="file2" id="file2" required class="mr-2">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Enviar</button>
        </form>
    </div>

    <div class="flex space-x-5">
        <div class="w-1/2">
            <h2 class="text-lg font-bold mb-2">Planilha 1</h2>
            <div class="overflow-auto h-80 border p-2">
                <table id="table1" class="min-w-full border-collapse border border-gray-300"></table>
            </div>
        </div>
        <div class="w-1/2">
            <h2 class="text-lg font-bold mb-2">Planilha 2</h2>
            <div class="overflow-auto h-80 border p-2">
                <table id="table2" class="min-w-full border-collapse border border-gray-300"></table>
            </div>
        </div>
    </div>

    <div class="mt-5 bg-white p-5 shadow rounded">
        <h2 class="text-lg font-bold mb-2">Comparação de Dados</h2>
        <div class="flex space-x-3 mb-2">
            <select id="coluna1" class="p-2 border rounded"></select>
            <select id="operador" class="p-2 border rounded">
                <option value="igual">Igual</option>
                <option value="maior">Maior que</option>
                <option value="menor">Menor que</option>
                <option value="diferenca">Diferença de valor</option>
            </select>
            <select id="coluna2" class="p-2 border rounded"></select>
            <button id="comparar" class="bg-green-500 text-white px-4 py-2 rounded">Comparar</button>
        </div>
        <div id="resultado" class="p-2 bg-gray-200 rounded"></div>
    </div>

    <script>
        let file1 = '', file2 = '';

        $("#upload-form").submit(function (e) {
            e.preventDefault();
            let formData = new FormData(this);

            $.ajax({
                url: "spreadsheet/upload",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    file1 = response.file1.split('/').pop();
                    file2 = response.file2.split('/').pop();
                    alert("Arquivos enviados!");
                    loadTable(response.file1, "#table1", "#coluna1");
                    loadTable(response.file2, "#table2", "#coluna2");
                }
            });
        });

        function loadTable(filename, tableId, selectId) {
            $.getJSON(`spreadsheet/data/${filename.split('/').pop()}`, function (data) {
                let table = $(tableId).empty();
                if (data.length === 0) return;

                let headers = Object.keys(data[0]);
                let thead = $("<thead>").appendTo(table);
                let headerRow = $("<tr>").appendTo(thead);

                $(selectId).empty();
                headers.forEach(col => {
                    $("<th>").text(col).addClass("border p-2").appendTo(headerRow);
                    $(selectId).append(`<option value="${col}">${col}</option>`);
                });

                let tbody = $("<tbody>").appendTo(table);
                data.forEach(row => {
                    let tr = $("<tr>").appendTo(tbody);
                    headers.forEach(col => {
                        $("<td>").text(row[col]).addClass("border p-2").appendTo(tr);
                    });
                });
            });
        }

        $("#comparar").click(function () {
            let coluna1 = $("#coluna1").val();
            let operador = $("#operador").val();
            let coluna2 = $("#coluna2").val();

            $.post("spreadsheet/compare", {
                file1: file1,
                file2: file2,
                coluna1: coluna1,
                coluna2: coluna2,
                operador: operador,
                _token: "{{ csrf_token() }}"
            }, function (response) {
                $("#resultado").empty();
                response.forEach(item => {
                    $("#resultado").append(`<div>${item.join(" - ")}</div>`);
                });
            });
        });
    </script>
</body>
</html>
