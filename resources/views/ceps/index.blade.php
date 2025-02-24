{{-- resources/views/ceps/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Meus CEPs</h1>

    {{-- Display success or error messages --}}
    @if (session('success'))
        <div class="mb-4 p-2 bg-green-200 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- Search form --}}
    <form action="{{ route('ceps.index') }}" method="GET" class="mb-4">
        <div class="flex items-center space-x-2">
            <select name="field" class="border border-gray-300 p-2 rounded text-black">
                <option value="cep" {{ request('field') == 'cep' ? 'selected' : '' }}>CEP</option>
                <option value="uf" {{ request('field') == 'uf' ? 'selected' : '' }}>UF</option>
                <option value="localidade" {{ request('field') == 'localidade' ? 'selected' : '' }}>Cidade</option>
                <option value="logradouro" {{ request('field') == 'logradouro' ? 'selected' : '' }}>Logradouro</option>                
            </select>
            <input
                type="text"
                name="search"
                placeholder="Buscar"
                class="border border-gray-300 p-2 w-full rounded text-black"
                value="{{ request('search') }}"
            >
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Buscar
            </button>
            <a href="{{ route('ceps.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Limpar
            </a>
        </div>
    </form>

    @if ($ceps->isEmpty())
        <p>Você não possui nenhum CEP salvo ainda!</p>
    @else
        <table>
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="px-4 py-2">CEP</th>
                    <th class="px-4 py-2">UF</th>
                    <th class="px-4 py-2">Cidade</th>
                    <th class="px-4 py-2">Logradouro</th>
                    <th class="px-4 py-2">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ceps as $cep)
                    <tr class="border-b border-gray-200">
                        <td class="px-4 py-2">{{ $cep->cep }}</td>
                        <td class="px-4 py-2">{{ $cep->uf }}</td>
                        <td class="px-4 py-2">{{ $cep->localidade }}</td>
                        <td class="px-4 py-2">{{ $cep->logradouro }}</td>
                        <td class="px-4 py-2 space-x-2">
                            {{-- Show link --}}
                            <a href="{{ route('ceps.show', $cep->id) }}" class="text-blue-500 hover:underline">Detalhes</a>
                            {{-- Delete form --}}
                            <form action="{{ route('ceps.destroy', $cep->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Deseja realmente deletar este CEP?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline">Deletar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="mt-4">
        <a href="{{ route('ceps.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Pesquisar novo CEP
        </a>
    </div>

    <!-- Pagination links -->
    <div class="mt-4">
        {{ $ceps->appends(request()->query())->links() }}
    </div>
</div>
@endsection
