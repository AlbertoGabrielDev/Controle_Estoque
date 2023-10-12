@extends('layouts.principal')
</head>
<body class="bg-gray-100">
  <div class="min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full p-6 bg-white rounded-lg shadow-lg">
      <div class="flex justify-center mb-8">
        <img src="https://www.emprenderconactitud.com/img/POC%20WCS%20(1).png" alt="Logo" class="w-30 h-20">
      </div>
      <h1 class="text-2xl font-semibold text-center text-gray-500 mt-8 mb-6">Registrar</h1>
      <form  method="POST" action="{{ route('register') }}">
      @csrf
        <div class="mb-6">
            <label for="name" value="{{ __('Name') }}" class="block mb-2 text-sm text-gray-600">Nome</label>
            <input id="name" name="name" :value="old('name')" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500" required>
        </div>
        <div class="mb-6">
            <label for="email" value="{{ __('Email') }}" class="block mb-2 text-sm text-gray-600">Email</label>
            <input type="email" id="email" :value="old('email')" name="email" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500" required  autocomplete="username">
        </div>
        <div class="mb-6">
            <label for="password" value="{{ __('Password') }}" class="block mb-2 text-sm text-gray-600">Senha</label>
            <input type="password" id="password" name="password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500" required autocomplete="new-password">
        </div>
        <div class="mb-6">
            <label for="password" value="{{ __('Confirm Password') }}" class="block mb-2 text-sm text-gray-600">Repetir Senha</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500" required autocomplete="new-password">
        </div>
        <button type="submit" class="w-32 bg-gradient-to-r from-cyan-400 to-cyan-600 text-white py-2 rounded-lg mx-auto block focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 mt-4 mb-6">{{ __('Registrar') }}</button>
      </form>
      <p class="text-xs text-gray-600 text-center mt-10">&copy; 2023 Verdurao</p>
    </div>
  </div>
</body>
</html>