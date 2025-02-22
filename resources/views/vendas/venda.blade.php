@extends('layouts.principal')

@section('conteudo')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Vendas em Tempo Real</h1>
    <div id="vendas-real-time" class="space-y-2">
        <!-- Itens serão adicionados via JS -->
    </div>
</div>

<template id="venda-template">
    <div class="p-3 bg-white shadow rounded-lg">
        <div class="flex justify-between items-center">
            <span class="font-semibold produto-nome"></span>
            <span class="text-sm text-gray-500" data-hora></span>
        </div>
        <div class="text-sm text-gray-600">Código: <span class="produto-codigo"></span></div>
    </div>
</template>
@endsection