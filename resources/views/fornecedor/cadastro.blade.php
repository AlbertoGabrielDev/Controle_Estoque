@extends('layouts.principal')

@section('conteudo')

<div class="produto">
    Cadastro de Fornecedores
</div>


<form action="{{route('marca.inserirMarca')}}" method="POST">
    @csrf
 <div class="estoque_espacamento"></div>
    <div class="row">
        <div class="col-md-4">
          <input type="text" class="form-control form-control-lg w-75" name="fornecedor" placeholder="Nome da Fornecedor">
        </div>
        <div class="col-md-4">
            <input type="text" class="form-control form-control-lg w-75" name="cnpj" placeholder="CNPJ">
          </div>
          <div class="col-md-4">
            <input type="text" class="form-control form-control-lg w-75" name="cep" placeholder="CEP">
          </div>
          <div class="col-md-4">
            <input type="text" class="form-control form-control-lg w-75" name="logradouro" placeholder="Logradouro">
          </div>
          <div class="col-md-4">
            <input type="text" class="form-control form-control-lg w-75" name="bairro" placeholder="Bairro">
          </div>
          <div class="col-md-4">
            <input type="text" class="form-control form-control-lg w-75" name="numero_casa" placeholder="Número">
          </div>
          <div class="col-md-4">
            <input type="text" class="form-control form-control-lg w-75" name="telefone" placeholder="Telefone">
          </div>
          <div class="col-md-4">
            <input type="text" class="form-control form-control-lg w-75" name="email" placeholder="Email">
          </div>
          <div class="col-md-4">
            {{-- {{dd($cidade)}} --}}
              <select class="form-control form-control-lg w-75" name="cidade" id="estado" required>
                  <option value="">Selecione um Estado</option>
                  @foreach ($estado as $estados)
                      <option value="{{ $estados->id }}">{{ $estados->nome }}</option>
                  @endforeach
              </select>
              <select id="cidade" disabled>
                <option value="">Selecione um estado primeiro</option>
            </select>
          </div>
        <div class="div_criar_marca">
            <button class="button_criar_marca" type="submit">Cadastrar Fornecedor</button>     
        </div>
    </div>
      
</form>

<script>
   $(function () {
        $('#estado').change(function () {
            var estadoId = $(this).val();
            
            if (estadoId) {
                $.ajax({
                    url: '/verdurao/fornecedor/cidade/' + estadoId,
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        $('#cidade').empty().append('<option value="">Selecione uma cidade</option>');

                        $.each(data, function (index, cidade) {
                            $('#cidade').append('<option value="' + cidade.uf + '">' + cidade.nome + '</option>');
                        });
                        
                        $('#cidade').prop('disabled', false);
                         
                    }
                });
            } else {
                $('#cidade').empty().append('<option value="">Selecione um estado primeiro</option>');
                $('#cidade').prop('disabled', true);
            }
        });
    });
</script>
@endsection
