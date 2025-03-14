{{-- resources/views/ceps/create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Procurar um CEP</h1>

    {{-- Display validation errors --}}
    @if ($errors->any())
        <div class="mb-4 p-2 bg-red-200 text-red-800 rounded">
            <ul>
                @foreach($errors->all() as $error)
                    <li>- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mb-4">
        <button id="toggleButton" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
            Procurar por UF, Localidade, Logradouro
        </button>
    </div>

    <form id="cepForm" action="{{ route('ceps.store') }}" method="POST" class="space-y-4">
        @csrf
        
        <div>
            <label for="cep" class="block font-semibold mb-1">CEP (exemplo: 38411848):</label>
            <input
                type="text"
                name="cep"
                id="cep"
                class="border border-gray-300 p-2 w-64 rounded text-black"
                value="{{ old('cep') }}"
            >
        </div>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Procurar e Salvar
        </button>
    </form>

    <form id="ufForm" action="{{ route('ceps.storeMultiple') }}" method="POST" class="space-y-4 hidden">
        @csrf
        
        <div>
            <label for="uf" class="block font-semibold mb-1">UF:</label>
            <input
                type="text"
                name="uf"
                id="uf"
                class="border border-gray-300 p-2 w-64 rounded text-black"
                value="{{ old('uf') }}"
            >
        </div>

        <div>
            <label for="localidade" class="block font-semibold mb-1">Localidade:</label>
            <input
                type="text"
                name="localidade"
                id="localidade"
                class="border border-gray-300 p-2 w-64 rounded text-black"
                value="{{ old('localidade') }}"
            >
        </div>

        <div>
            <label for="logradouro" class="block font-semibold mb-1">Logradouro:</label>
            <input
                type="text"
                name="logradouro"
                id="logradouro"
                class="border border-gray-300 p-2 w-64 rounded text-black"
                value="{{ old('logradouro') }}"
            >
        </div>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Procurar e Salvar
        </button>
    </form>
</div>

<script>
    document.getElementById('toggleButton').addEventListener('click', function() {
        var cepForm = document.getElementById('cepForm');
        var ufForm = document.getElementById('ufForm');
        if (cepForm.classList.contains('hidden')) {
            cepForm.classList.remove('hidden');
            ufForm.classList.add('hidden');
            this.textContent = 'Procurar por UF, Localidade, Logradouro';
        } else {
            cepForm.classList.add('hidden');
            ufForm.classList.remove('hidden');
            this.textContent = 'Procurar por CEP';
        }
    });
</script>
@endsection