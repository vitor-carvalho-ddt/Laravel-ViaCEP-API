<?php

namespace App\Services;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\ConnectionException;
class ViaCEPService
{
    public function getCEPData(string $url): arrasiy {
        try{
            $response = Http::timeout(seconds: 5)->get(url: $url);
            
            if($response->failed()){
                Log::error(message: 'Failed to fetch CEP data', context: [
                    'url' => $url,
                    'response' => $response->body()
                ]);
                return ['error' => 'Dados do CEP inválidos!'];
            }

            return $response->json();
        }
        catch(ConnectionException $e){
            Log::error(message: 'Request to ViaCEP timed out', context: [
                'url' => $url,
                'exception' => $e->getMessage()
            ]);
            return ['error' => 'Tempo limite excedido ao tentar se comunicar com o servidor ViaCEP!'];
        }
    }
}