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

        <div class="mb-5 bg-white p-5 shadow rounded flex justify-center">
        <form id="upload-form" enctype="multipart/form-data" class="flex flex-col items-center space-y-3 w-72">
            @csrf

            <label class="w-64 flex flex-col items-center px-3 py-3 bg-gray-100 text-blue-500 rounded-md shadow-md tracking-wide uppercase border border-blue-300 cursor-pointer hover:bg-blue-200 hover:text-blue-700 transition duration-300">
                <svg class="w-6 h-6 mb-1" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M16.88 7.88l-6-6a1 1 0 00-1.42 0l-6 6A1 1 0 004 9h2v5a2 2 0 002 2h6a2 2 0 002-2V9h2a1 1 0 00.71-1.71zM14 9v5H6V9H4.41L10 3.41 15.59 9H14z"></path>
                </svg>
                <span class="text-xs font-medium">Escolha o primeiro arquivo</span>
                <input type="file" name="file1" id="file1" required class="hidden">
            </label>

            <label class="w-64 flex flex-col items-center px-3 py-3 bg-gray-100 text-blue-500 rounded-md shadow-md tracking-wide uppercase border border-blue-300 cursor-pointer hover:bg-blue-200 hover:text-blue-700 transition duration-300">
                <svg class="w-6 h-6 mb-1" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M16.88 7.88l-6-6a1 1 0 00-1.42 0l-6 6A1 1 0 004 9h2v5a2 2 0 002 2h6a2 2 0 002-2V9h2a1 1 0 00.71-1.71zM14 9v5H6V9H4.41L10 3.41 15.59 9H14z"></path>
                </svg>
                <span class="text-xs font-medium">Escolha o segundo arquivo</span>
                <input type="file" name="file2" id="file2" required class="hidden">
            </label>

            <button type="submit" class="w-64 bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-md transition duration-300">
                Enviar
            </button>
        </form>
    </div>


        <div class="flex space-x-5">
            <!-- Planilha 1 -->
            <div class="w-1/2">
                <div class="flex justify-between items-center mb-2">
                    <h2 class="text-lg font-bold">Planilha 1</h2>
                    <div class="relative">
                        <button onclick="toggleDropdown('dropdown1')" class="bg-blue-500 text-white px-3 py-1 rounded">Colunas ▼</button>
                        <div id="dropdown1" class="absolute right-0 mt-2 w-48 bg-white border shadow-lg hidden max-h-48 overflow-y-auto">
                            <div id="colunas-controle1" class="p-2"></div>
                        </div>
                    </div>
                </div>
                <div class="overflow-auto h-80 border p-2">
                    <table id="table1" class="min-w-full border-collapse border border-gray-300"></table>
                </div>
            </div>

            <!-- Planilha 2 -->
            <div class="w-1/2">
                <div class="flex justify-between items-center mb-2">
                    <h2 class="text-lg font-bold">Planilha 2</h2>
                    <div class="relative">
                        <button onclick="toggleDropdown('dropdown2')" class="bg-blue-500 text-white px-3 py-1 rounded">Colunas ▼</button>
                        <div id="dropdown2" class="absolute right-0 mt-2 w-48 bg-white border shadow-lg hidden max-h-48 overflow-y-auto">
                            <div id="colunas-controle2" class="p-2"></div>
                        </div>
                    </div>
                </div>
                <div class="overflow-auto h-80 border p-2">
                    <table id="table2" class="min-w-full border-collapse border border-gray-300"></table>
                </div>
            </div>
        </div>
        <div class="mt-5 bg-white p-5 shadow rounded">
            <h2 class="text-lg font-bold mb-2">Comparação de Dados</h2>
            <div class="flex flex-wrap gap-3 mb-2">
                <select id="coluna1" class="p-2 border rounded bg-gray-50"></select>
                <select id="operador" class="p-2 border rounded bg-gray-50">
                    <option value="igual">Igual</option>
                    <option value="maior">Maior que</option>
                    <option value="menor">Menor que</option>
                    <option value="diferenca">Diferença de valor</option>
                </select>
                <select id="coluna2" class="p-2 border rounded bg-gray-50"></select>
                <button id="comparar"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded transition duration-300">
                    Comparar
                </button>
            </div>
            <div id="resultado" class="p-2 bg-gray-200 rounded overflow-auto max-h-64"></div>
        </div>


        <script>
            let file1 = '',
                file2 = '';

            $("#comparar").click(function() {
                let coluna1 = $("#coluna1").val();
                let operador = $("#operador").val();
                let coluna2 = $("#coluna2").val();

                if (!coluna1 || !coluna2) {
                    alert("Selecione ambas as colunas para comparar.");
                    return;
                }

                $.post("spreadsheet/compare", {
                    file1: file1,
                    file2: file2,
                    coluna1: coluna1,
                    coluna2: coluna2,
                    operador: operador,
                    _token: "{{ csrf_token() }}"
                }, function(response) {
                    $("#resultado").empty();
                    if (response.length === 0) {
                        $("#resultado").append("<p class='text-red-500'>Nenhum resultado encontrado.</p>");
                    } else {
                        response.forEach(item => {
                            $("#resultado").append(`<div class="p-2 bg-white border rounded my-1">${item.join(" - ")}</div>`);
                        });
                    }
                });
            });

            $("#upload-form").submit(function(e) {
                e.preventDefault();
                let formData = new FormData(this);

                $.ajax({
                    url: "spreadsheet/upload",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        file1 = response.file1.split('/').pop();
                        file2 = response.file2.split('/').pop();
                        alert("Arquivos enviados e processamento iniciado!");
                        loadTable(response.file1, "#table1", "#colunas-controle1", "table1");
                        loadTable(response.file2, "#table2", "#colunas-controle2", "table2");
                    }
                });
            });

            function loadTable(filename, tableId, controlId, tableKey) {
                $.getJSON(`spreadsheet/data/${filename.split('/').pop()}`, function(data) {
                    let table = $(tableId).empty();
                    if (data.length === 0) return;

                    let headers = Object.keys(data[0]);
                    let thead = $("<thead>").appendTo(table);
                    let headerRow = $("<tr>").appendTo(thead);

                    headers.forEach(col => {
                        $("<th>").text(col)
                            .addClass("border p-2 cursor-pointer")
                            .attr("data-col", col)
                            .click(function() {
                                sortTable(tableId, col);
                            }) // Adicionando evento de clique para ordenação ✅
                            .appendTo(headerRow);
                    });

                    let tbody = $("<tbody>").appendTo(table);
                    data.forEach(row => {
                        let tr = $("<tr>").appendTo(tbody);
                        headers.forEach(col => {
                            $("<td>").text(row[col]).addClass("border p-2").attr("data-col", col).appendTo(tr);
                        });
                    });

                    generateColumnControls(headers, tableId, controlId);
                    updateComparisonDropdowns(headers, tableKey);
                });
            }

            // Atualiza os selects de colunas na comparação
            function updateComparisonDropdowns(headers, tableKey) {
                let select = tableKey === "table1" ? "#coluna1" : "#coluna2";
                $(select).empty();
                headers.forEach(col => {
                    $(select).append(`<option value="${col}">${col}</option>`);
                });
            }

            // Função para ordenação ao clicar no cabeçalho
            function sortTable(tableId, column) {
                let rows = $(`${tableId} tbody tr`).toArray();
                let index = $(`${tableId} th[data-col='${column}']`).index();
                let asc = !$(`${tableId} th[data-col='${column}']`).hasClass("sorted-asc");

                rows.sort((a, b) => {
                    let cellA = $(a).find(`td:eq(${index})`).text().trim();
                    let cellB = $(b).find(`td:eq(${index})`).text().trim();
                    let valA = isNaN(cellA) ? cellA.toLowerCase() : parseFloat(cellA);
                    let valB = isNaN(cellB) ? cellB.toLowerCase() : parseFloat(cellB);

                    return asc ? (valA > valB ? 1 : -1) : (valA < valB ? 1 : -1);
                });

                $(`${tableId} th`).removeClass("sorted-asc sorted-desc");
                $(`${tableId} th[data-col='${column}']`).addClass(asc ? "sorted-asc" : "sorted-desc");

                $(`${tableId} tbody`).empty().append(rows);
            }

            function generateColumnControls(headers, tableId, controlId) {
                if (!controlId.startsWith("#")) {
                    controlId = `#${controlId}`;
                }

                $(controlId).empty();

                headers.forEach(col => {
                    let control = $(`<div class="flex items-center space-x-2">
                    <input type="checkbox" checked data-col="${col}" class="col-toggle">
                    <label>${col}</label>
                </div>`);

                    control.find("input").change(function() {
                        let colName = $(this).data("col");
                        let index = $(`${tableId} th[data-col='${colName}']`).index() + 1;
                        $(`${tableId} th:nth-child(${index}), ${tableId} td:nth-child(${index})`).toggle();
                    });

                    $(controlId).append(control);
                });
            }

            function toggleDropdown(id) {
                $(".absolute").not(`#${id}`).hide();
                $(`#${id}`).toggle();
            }

            $(document).click(function(event) {
                if (!$(event.target).closest(".relative").length) {
                    $(".absolute").hide();
                }
            });
        </script>

        <style>
            .cursor-pointer {
                cursor: pointer;
            }

            .absolute {
                z-index: 10;
                display: none;
            }

            .sorted-asc::after {
                content: " ↑";
            }

            .sorted-desc::after {
                content: " ↓";
            }

            /* Estilização do Scroll */
            .overflow-y-auto::-webkit-scrollbar {
                width: 8px;
            }

            .overflow-y-auto::-webkit-scrollbar-thumb {
                background-color: #888;
                border-radius: 4px;
            }

            .overflow-y-auto::-webkit-scrollbar-track {
                background-color: #f1f1f1;
            }
        </style>



        <style>
            .cursor-pointer {
                cursor: pointer;
            }

            .absolute {
                z-index: 10;
                display: none;
            }

            /* Estilização do Scroll */
            .overflow-y-auto::-webkit-scrollbar {
                width: 8px;
            }

            .overflow-y-auto::-webkit-scrollbar-thumb {
                background-color: #888;
                border-radius: 4px;
            }

            .overflow-y-auto::-webkit-scrollbar-track {
                background-color: #f1f1f1;
            }
        </style>

    </body>

    </html>