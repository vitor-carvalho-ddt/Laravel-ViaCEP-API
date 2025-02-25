<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Repositories\Interfaces\CepRepositoryInterface;
use Illuminate\Http\Request;
use App\Models\Cep;
use App\Services\ViaCEPService;

class CepService
{
    protected $cepRepository;

    public function __construct(CepRepositoryInterface $cepRepository, ViaCEPService $ViaCEPService)
    {
        $this->cepRepository = $cepRepository;
        $this->ViaCEPService = $ViaCEPService;
    }

    public function getAllCeps($userId, $search = null, $field = null)
    {
        $query = $this->cepRepository->allQueryBuilder($userId);

        if ($search && $field) {
            $query = $query->where($field, 'like', "%{$search}%");
        }

        return $query->paginate(10);
    }

    public function getCepDetails(Cep $cep, $userId)
    {
        if ($cep->user_id !== $userId) {
            abort(403, 'Unauthorized');
        }

        return $cep;
    }

    public function createCep(Request $request)
    {
        $request->validate([
            'cep' => 'required|max:9',
        ]);

        $cleanCep = preg_replace('/\D/', '', $request->cep);

        $response = $this->ViaCEPService->getCEPDataUsingCEP($cleanCep);
        
        if ($response->failed() || isset($response['erro'])) {
            return ['error' => 'CEP not found or invalid.'];
        }

        $data = $response->json();

        $existingCep = $this->cepRepository->findByColumn('cep', $data['cep'] ?? $request->cep, auth()->id());
        if ($existingCep) {
            return ['error' => 'You already have this CEP saved.'];
        }

        $this->cepRepository->create([
            'user_id'    => auth()->id(),
            'cep'        => $data['cep'] ?? $request->cep,
            'logradouro' => $data['logradouro'] ?? null,
            'complemento'=> $data['complemento'] ?? null,
            'unidade'    => $data['unidade'] ?? null,
            'bairro'     => $data['bairro'] ?? null,
            'localidade' => $data['localidade'] ?? null,
            'uf'         => $data['uf'] ?? null,
            'estado'     => $data['estado'] ?? null,
            'regiao'     => $data['regiao'] ?? null,
            'ibge'       => $data['ibge'] ?? null,
            'gia'        => $data['gia'] ?? null,
            'ddd'        => $data['ddd'] ?? null,
            'siafi'      => $data['siafi'] ?? null
        ]);

        return ['success' => 'CEP salvo com sucesso!'];
    }

    public function createMultipleCeps(Request $request)
    {
        $request->validate([
            'uf' => 'required|max:2',
            'localidade' => 'required|min:3|max:50',
            'logradouro' => 'required|min:3|max:50',
        ]);

        $state = $request->uf;
        $city = $request->localidade;
        $address = $request->logradouro;
        
        $response = $this->ViaCEPService->getCEPDataUsingAddress($state, $city, $address);

        if ($response->failed() || isset($response['erro'])) {
            return ['error' => 'Dados inválidos!'];
        }

        $data = $response->json();

        foreach ($data as $cepData) {
            $existingCep = $this->cepRepository->findByColumn('cep', $cepData['cep'] ?? $request->cep, auth()->id());
            if ($existingCep) {
                continue;
            }

            $this->cepRepository->create([
                'user_id'    => auth()->id(),
                'cep'        => $cepData['cep'] ?? $request->cep,
                'logradouro' => $cepData['logradouro'] ?? null,
                'complemento'=> $cepData['complemento'] ?? null,
                'unidade'    => $cepData['unidade'] ?? null,
                'bairro'     => $cepData['bairro'] ?? null,
                'localidade' => $cepData['localidade'] ?? null,
                'uf'         => $cepData['uf'] ?? null,
                'estado'     => $cepData['estado'] ?? null,
                'regiao'     => $cepData['regiao'] ?? null,
                'ibge'       => $cepData['ibge'] ?? null,
                'gia'        => $cepData['gia'] ?? null,
                'ddd'        => $cepData['ddd'] ?? null,
                'siafi'      => $cepData['siafi'] ?? null
            ]);
        }

        return ['success' => 'CEPs salvos com sucesso!'];
    }

    public function deleteCep(Cep $cep, $userId)
    {
        if ($cep->user_id !== $userId) {
            abort(403);
        }

        $this->cepRepository->delete($cep->id, $userId);

        return ['success' => 'CEP deletado com sucesso!'];
    }
}