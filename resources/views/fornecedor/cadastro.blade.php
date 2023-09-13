@extends('layouts.principal')

@section('conteudo')

<div class="produto">
    Cadastro de Fornecedores
</div>


<form action="{{route('fornecedor.inserirCadastro')}}" method="POST" id="cadastro_fornecedor">
    @csrf
 <div class="estoque_espacamento"></div>

    <div class="row">
      <div class="col-md-4">
        <input type="text" class="form-control form-control-lg w-75" required name="nome_fornecedor" placeholder="Nome da Fornecedor">
      </div>
      <div class="col-md-4">
          <input type="text" class="form-control form-control-lg w-75" required name="cnpj" placeholder="CNPJ">
      </div>
      <div class="col-md-4">
          <input type="text" class="form-control form-control-lg w-75" required name="cep" placeholder="CEP">
      </div>
      <div class="col-md-4">
          <input type="text" class="form-control form-control-lg w-75" required name="logradouro" placeholder="Logradouro">
      </div>
      <div class="col-md-4">
          <input type="text" class="form-control form-control-lg w-75" required name="bairro" placeholder="Bairro">
      </div>
      <div class="col-md-4">
          <input type="text" class="form-control form-control-lg w-75" required name="numero_casa" placeholder="Número">
      </div>
      {{-- <div class="col-md-4">
          <input type="text" class="form-control form-control-lg w-75" required name="telefone" placeholder="Telefone">
      </div> --}}
      <div class="col-md-4">
          <input type="text" class="form-control form-control-lg w-75" required name="email" placeholder="Email">
      </div>
      <div class="col-md-4">
            <input type="text" class="form-control form-control-lg w-75" required name="ddd" placeholder="DDD">
            </div>
        <div class="col-md-4">
            <input type="text" class="form-control form-control-lg w-75" required name="telefone" placeholder="Telefone">
            <div class="btn-group" role="group" aria-label="Basic checkbox toggle button group">
                <input type="checkbox" class="btn-check" id="btncheck1" name="principal" value="1" autocomplete="off">
                <label class="btn btn-outline-primary" for="btncheck1">Principal</label>
            </div>
            <div class="btn-group" role="group" aria-label="Basic checkbox toggle button group">
                <input type="checkbox" class="btn-check" id="btncheck2" name="whatsapp" value="1" autocomplete="off">
                <label class="btn btn-outline-primary" for="btncheck2">Whatsapp</label>
            </div>
            <div class="btn-group" role="group" aria-label="Basic checkbox toggle button group">
                <input type="checkbox" class="btn-check" id="btncheck3" name="telegram" value="1" autocomplete="off">
                <label class="btn btn-outline-primary" for="btncheck3">Telegram</label>
            </div>
        <div>
  </div>

  <div class="select">
    <select class="" name="estado" id="estado" required>
        <option value="">Selecione um Estado</option>
        @foreach ($estado as $estados)
            <option value="{{ $estados->id }}">{{ $estados->nome }}</option>
        @endforeach
    </select>
    <select id="cidades" name="cidades" class="" disabled>
      <option value="">Selecione um estado primeiro</option>
    </select>
  </div>

  <div class="select">
    <label for="status">Status:</label>
    <select name="status" id="status" class="form-control">
        <option value="1">Ativo</option>
        <option value="0">Inativo</option>
    </select>
    </div>

  <div class="div_criar_fornecedor">
    <button class="button_criar_fornecedor" type="submit">Cadastrar Fornecedor</button>     
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
                        $('#cidades').empty().append('<option value="">Selecione uma cidade</option>');
                        //var cidadeId = $('#cidades').val();
                        $.each(data, function (index, cidade) {
                            $('#cidades').append('<option value="' + cidade.id + '">' + cidade.nome + '</option>');
                        });
                        
                        $('#cidades').prop('disabled', false);
                         
                    }
                });
            } else {
                $('#cidades').empty().append('<option value="">Selecione um estado primeiro</option>');
                $('#cidades').prop('disabled', true);
            }
        });
    });
</script>
@endsection

