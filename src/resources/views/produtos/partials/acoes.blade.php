<!-- resources/views/produtos/partials/acoes.blade.php -->
<x-edit-button :route="'produtos.editar'" :modelId="$produto->id_produto" />
<x-button-status :modelId="$produto->id_produto" :status="$produto->status" modelName="produto" />
