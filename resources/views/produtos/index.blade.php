@extends('layouts.principal')

@section('conteudo')
<div class="bg-white p-4 rounded-md w-full">
  <h5 class="mx-auto m-5 text-4xl font-medium text-slate-700 flex justify-center">Index</h5>
  <div class= "bg-white p-4 rounded-md w-full flex justify-between">
    <a class=" text-gray-500 py-2.5 px-4 relative mx-5 my-4 w-1/12 rounded hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white" href="{{route('categoria.inicio')}}">
      <i class="fa fa-angle-left mr-2"></i>Voltar
    </a>
    <a class=" text-gray-500 py-2.5 px-4 relative mx-5 my-4 w-1/12 rounded hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white" href="{{route('produtos.cadastro')}}">
      <i class="fas fa-plus mr-2"></i>Cadastrar
    </a>
  </div>
  
  <form action="{{ route('produtos.buscar') }}" method="GET" class="relative w-6/12">
    <div class="relative w-full">
      <input type="text" name="nome_produto" class="w-5/12 h-10 pl-10 text-base placeholder-gray-500 border rounded-full focus:shadow-outline" placeholder="Digite o nome do Produto">
      <button class="w-2/12 h-10 text-base placeholder-gray-500 border rounded-full focus:shadow-outline" type="submit">Pesquisar</button>
    </div>
  </form>

  <table class="w-full table-auto">
      <thead>
        <tr class="text-sm leading-normal">
          <th class="p-4 uppercase text-sm text-grey-dark border-b border-grey-light text-left">Cod. Produto</th>
          <th class="p-4 uppercase text-sm text-grey-dark border-b border-grey-light text-left">Nome Produto</th>
          <th class="p-4 uppercase text-sm text-grey-dark border-b border-grey-light text-left">Descrição</th>
          <th class="p-4 uppercase text-sm text-grey-dark border-b border-grey-light text-left">Unidade de Medida</th>
          <th class="p-4 uppercase text-sm text-grey-dark border-b border-grey-light text-left">Infor. Nutricional</th>
          <!-- <th data-order="asc" data-col="data_validade">Data de Validade</th> -->
          <th class="p-4 uppercase text-sm text-grey-dark border-b border-grey-light text-left">Editar</th>
          @can('permissao')
            <th class="p-4 uppercase text-sm text-grey-dark border-b border-grey-light text-left">Inativar</th>
          @endcan
        </tr>
      </thead>
      <tbody>
        @foreach ($produtos as $produto)
            <tr class="hover:bg-grey-lighter">
              <td class="p-4 border-b border-grey-light text-left">{{$produto->cod_produto}}</td>
              <td class="p-4 border-b border-grey-light text-left">{{$produto->nome_produto}}</td>
              <td class="p-4 border-b border-grey-light text-left">{{$produto->descricao}}</td>
              <td class="p-4 border-b border-grey-light text-left">{{$produto->unidade_medida}}</td>
              <td class="p-4 border-b border-grey-light text-left" x-data="{ reportsOpen: false }">
                  <div @click="reportsOpen = !reportsOpen" class="flex items-center text-gray-600 overflow-hidden mb-5 mx-auto">
                    <div class="w-10 px-2 transform transition duration-300 ease-in-out" :class="{'rotate-90': reportsOpen,' -translate-y-0.0': !reportsOpen }">
                      <i class="fa fa-angle-down" aria-hidden="true"></i>       
                    </div>
                    <div class='flex items-center px-2 py-3'>
                      <button class="hover:underline">Informações Nútricionais</button>
                    </div>
                  </div>
                  <div class="flex p-5 md:p-0 w-full transform transition duration-300 ease-in-out pb-10"
                  x-cloak x-show="reportsOpen" x-collapse x-collapse.duration.500ms >
                    {{json_decode($produto->inf_nutrientes)}}
                  </div>
              </td>
              <!-- <td class= "expiration-date" id="data">{{($produto->validade)}}</td> -->
              <td class="p-4 border-b border-grey-light text-left"><a href="{{route('produtos.editar', $produto->id_produto)}}">Editar</a></td>
              <td class="p-4 border-b border-grey-light text-left">
                @can('permissao')
                  <button class="toggle-ativacao @if($produto->status === 1) btn-danger @elseif($produto->status === 0) btn-success @else btn-primary @endif"
                    data-id="{{ $produto->id_produto }}">
                      {{ $produto->status ? 'Inativar' : 'Ativar' }}
                  </button>
                @endcan
              </td>
            </tr>
          @endforeach
      </tbody>
  </table>
</div>

<nav class="Page navigation example">
  <ul class="pagination">
    {{ $produtos->links()}}
  </ul>
</nav>

 <!-- <script>
 $(document).ready(function() {
    $('th[data-order]').click(function() {
        var column = $(this).data('col');
        var currentOrder = $(this).data('order');
        var newOrder = currentOrder === 'asc' ? 'desc' : 'asc';
        $('th[data-order]').removeClass('asc desc');
        $(this).addClass(newOrder);
        window.location.href = '{{ route("produtos.index") }}?ordenar=' + column + '&direcao=' + newOrder;
    });
});
</script>  -->
@endsection