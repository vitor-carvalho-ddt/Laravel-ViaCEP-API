<?php

namespace App\Services;
use Illuminate\Support\Facades\Http;
class ViaCEPService
{
    public function getCEPDataUsingCEP($cep){
        return Http::get("https://viacep.com.br/ws/{$cep}/json/");
    }

    public function getCEPDataUsingAddress($state, $city, $address){
        return Http::get("https://viacep.com.br/ws/{$state}/{$city}/{$address}/json/");
    }
}