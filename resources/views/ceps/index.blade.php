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

    @if (is_null($ceps) or $ceps->isEmpty())
        <p>Você não possui nenhum CEP salvo ainda!</p>
    @else
        <table class="">
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="px-4 py-2">CEP</th>
                    <th class="px-4 py-2">Cidade</th>
                    <th class="px-4 py-2">UF</th>
                    <th class="px-4 py-2">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ceps as $cep)
                    <tr class="border-b border-gray-200">
                        <td class="px-4 py-2">{{ $cep->cep }}</td>
                        <td class="px-4 py-2">{{ $cep->localidade }}</td>
                        <td class="px-4 py-2">{{ $cep->uf }}</td>
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
</div>
@endsection
