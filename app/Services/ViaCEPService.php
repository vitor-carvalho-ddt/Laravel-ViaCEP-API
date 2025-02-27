<?php

namespace App\Services;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;
class ViaCEPService
{
    public function getCEPData(string $url): array {
        try{
            $response = Http::timeout(seconds: 5)->get(url: $url);
            
            if($response->failed()){
                return ['error' => 'Dados do CEP inválidos!'];
            }

            return $response->json();
        }
        catch(ConnectionException $e){
            return ['error' => 'Tempo limite excedido ao tentar se comunicar com o servidor ViaCEP!'];
        }
    }
}