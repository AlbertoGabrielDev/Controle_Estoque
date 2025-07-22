@extends('layouts.principal')

@section('conteudo')
<div class="bg-white p-4 rounded-md w-full max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-semibold text-slate-700">Cadastro de Fornecedor</h1>
        <a href="{{ route('fornecedor.index') }}"
            class="flex items-center px-4 py-2 bg-gray-100 hover:bg-cyan-500 text-gray-600 hover:text-white rounded-md transition-colors">
            <i class="fa fa-angle-left mr-2"></i>Voltar
        </a>
    </div>

    <form action="{{ route('fornecedor.inserirCadastro') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Primeira Linha -->
        <div class="grid md:grid-cols-2 gap-6">
            <div class="form-group">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome Fornecedor</label>
                <input type="text"
                    name="nome_fornecedor"
                    class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-cyan-500"
                    value="{{ old('nome_fornecedor') }}"
                    required>
                @error('nome_fornecedor')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="block text-sm font-medium text-gray-700 mb-1">CNPJ</label>
                <input type="text"
                    name="cnpj"
                    id="cnpj"
                    class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-cyan-500"
                    value="{{ old('cnpj') }}"
                    required>
                @error('cnpj')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Segunda Linha -->
        <div class="grid md:grid-cols-2 gap-6">
            <div class="form-group">
                <label class="block text-sm font-medium text-gray-700 mb-1">CEP</label>
                <input type="text"
                    name="cep"
                    id="cep"
                    class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-cyan-500"
                    value="{{ old('cep') }}"
                    required>
                @error('cep')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="block text-sm font-medium text-gray-700 mb-1">Logradouro</label>
                <input type="text"
                    name="logradouro"
                    id="endereco"
                    class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-cyan-500"
                    value="{{ old('logradouro') }}"
                    required>
                @error('logradouro')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Terceira Linha -->
        <div class="grid md:grid-cols-2 gap-6">
            <div class="form-group">
                <label class="block text-sm font-medium text-gray-700 mb-1">Bairro</label>
                <input type="text"
                    name="bairro"
                    id="bairro"
                    class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-cyan-500"
                    value="{{ old('bairro') }}"
                    required>
                @error('bairro')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="block text-sm font-medium text-gray-700 mb-1">Número</label>
                <input type="text"
                    name="numero_casa"
                    class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-cyan-500"
                    value="{{ old('numero_casa') }}"
                    required>
                @error('numero_casa')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Quarta Linha -->
        <div class="grid md:grid-cols-2 gap-6">
            <div class="form-group">
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email"
                    name="email"
                    class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-cyan-500"
                    value="{{ old('email') }}"
                    required>
                @error('email')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="block text-sm font-medium text-gray-700 mb-1">DDD</label>
                <input type="text"
                    name="ddd"
                    class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-cyan-500"
                    value="{{ old('ddd') }}"
                    required>
                @error('ddd')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Quinta Linha -->
        <div class="grid md:grid-cols-2 gap-6">
            <div class="form-group">
                <label class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
                <input type="text"
                    name="cidade"
                    id="cidade"
                    class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-cyan-500"
                    value="{{ old('cidade') }}"
                    required>
                @error('cidade')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="block text-sm font-medium text-gray-700 mb-1">UF</label>
                <input type="text"
                    name="uf"
                    id="uf"
                    class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-cyan-500"
                    value="{{ old('uf') }}"
                    required>
                @error('uf')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Sexta Linha -->
        <div class="grid md:grid-cols-2 gap-6">
            <div class="form-group">
                <label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                <input type="text"
                    name="telefone"
                    class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-cyan-500"
                    value="{{ old('telefone') }}"
                    required>
                @error('telefone')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Checkboxes Estilizados -->
            <div class="grid md:grid-cols-3 gap-4">
                <div class="group relative">
                    <input type="checkbox"
                        name="principal"
                        id="principal"
                        class="absolute opacity-0 peer cursor-pointer"
                        value="1">
                    <label for="principal"
                        class="flex items-center p-4 border rounded-lg cursor-pointer transition-all
                                  bg-white hover:border-cyan-300 peer-checked:border-red-500 peer-checked:bg-red-50
                                  peer-focus:ring-2 peer-focus:ring-red-200">
                        <div class="flex items-center space-x-3">
                            <span class="text-gray-700 font-medium">Principal</span>
                        </div>
                    </label>
                </div>

                <div class="group relative">
                    <input type="checkbox"
                        name="whatsapp"
                        id="whatsapp"
                        class="absolute opacity-0 peer cursor-pointer"
                        value="1">
                    <label for="whatsapp"
                        class="flex items-center p-4 border rounded-lg cursor-pointer transition-all
                                  bg-white hover:border-cyan-300 peer-checked:border-green-500 peer-checked:bg-green-50
                                  peer-focus:ring-2 peer-focus:ring-green-200">
                        <div class="flex items-center space-x-3">
                            <span class="text-gray-700 font-medium">Whatsapp</span>
                        </div>
                    </label>
                </div>

                <div class="group relative">
                    <input type="checkbox"
                        name="telegram"
                        id="telegram"
                        class="absolute opacity-0 peer cursor-pointer"
                        value="1">
                    <label for="telegram"
                        class="flex items-center p-4 border rounded-lg cursor-pointer transition-all
                                  bg-white hover:border-cyan-300 peer-checked:border-indigo-500 peer-checked:bg-indigo-50
                                  peer-focus:ring-2 peer-focus:ring-indigo-200">
                        <div class="flex items-center space-x-3">
                            <span class="text-gray-700 font-medium">Telegram</span>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Botão de Submit -->
        <button type="submit"
            class="w-full md:w-auto px-6 py-2 bg-cyan-500 hover:bg-cyan-600 text-white rounded-md transition-colors">
            <i class="fas fa-plus mr-2"></i>Cadastrar Fornecedor
        </button>
    </form>
</div>
@endsection