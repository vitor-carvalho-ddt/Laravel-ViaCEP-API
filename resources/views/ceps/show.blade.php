{{-- resources/views/ceps/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">CEP Details</h1>

    <div class="space-y-2">
        <p><strong>CEP:</strong> {{ $cep->cep }}</p>
        <p><strong>Logradouro:</strong> {{ $cep->logradouro }}</p>
        <p><strong>Complemento:</strong> {{ $cep->complemento }}</p>
        <p><strong>Bairro:</strong> {{ $cep->bairro }}</p>
        <p><strong>Localidade:</strong> {{ $cep->localidade }}</p>
        <p><strong>UF:</strong> {{ $cep->uf }}</p>
        <p><strong>Estado:</strong> {{ $cep->estado }}</p>
        <p><strong>Região:</strong> {{ $cep->regiao }}</p>
        <p><strong>IBGE:</strong> {{ $cep->ibge }}</p>
        <p><strong>GIA:</strong> {{ $cep->gia }}</p>
        <p><strong>DDD:</strong> {{ $cep->ddd }}</p>
        <p><strong>SIAFI:</strong> {{ $cep->siafi }}</p>
    </div>

    <div class="mt-4">
        <a href="{{ route('ceps.index') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Voltar à lista de CEPs
        </a>
    </div>
</div>
@endsection
