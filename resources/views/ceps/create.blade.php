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

    <form action="{{ route('ceps.store') }}" method="POST" class="space-y-4">
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
</div>
@endsection
