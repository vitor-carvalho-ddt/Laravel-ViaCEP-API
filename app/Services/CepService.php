<?php

namespace App\Services;

use App\Http\Requests\CreateCepRequest;
use App\Http\Requests\CreateMultipleCepsRequest;
use App\Repositories\Interfaces\CepRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Cep;
use App\Services\ViaCEPService;

class CepService
{

    public function __construct(private readonly CepRepositoryInterface $cepRepository, private readonly ViaCEPService $ViaCEPService)
    {
    }

    public function getAllCeps($userId, $search = null, $field = null): LengthAwarePaginator
    {
        $query = $this->cepRepository->allQueryBuilder(userId: $userId);

        if ($search && $field) {
            $query = $query->where($field, 'like', "%{$search}%");
        }

        return $query->paginate(10);
    }

    public function getCepDetails(Cep $cep, $userId): Cep
    {
        if ($cep->user_id !== $userId) {
            abort(code: 403, message: 'Unauthorized');
        }

        return $cep;
    }

    public function createCep(CreateCepRequest $request): array
    {
        $cleanCep = preg_replace(pattern: '/\D/', replacement: '', subject: $request->cep);
        $url = "viacep.com.br/ws/{$cleanCep}/json/";
        $response = $this->ViaCEPService->getCEPData(url: $url);

        if (isset($response['error'])) {
            return ['error' => $response['error']];
        }

        $data = $response;

        $existingCep = $this->cepRepository->findByColumn(column: 'cep', value: $data['cep'] ?? $request->cep, userId: auth()->id());
        if ($existingCep) {
            return ['error' => 'Você já possui este CEP salvo!'];
        }

        $this->cepRepository->create(data: [
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

    public function createMultipleCeps(CreateMultipleCepsRequest $request): array
    {
        $state = $request->uf;
        $city = $request->localidade;
        $address = $request->logradouro;
        
        $url = "https://viacep.com.br/ws/{$state}/{$city}/{$address}/json/";
        $response = $this->ViaCEPService->getCEPData(url: $url);

        if (isset($response['erro'])) {
            return ['error' => $response['error']];
        }

        $data = $response;

        foreach ($data as $cepData) {
            $existingCep = $this->cepRepository->findByColumn(column: 'cep', value: $cepData['cep'] ?? $request->cep, userId: auth()->id());
            if ($existingCep) {
                continue;
            }

            $this->cepRepository->create(data: [
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

    public function deleteCep(Cep $cep, $userId): array
    {
        if ($cep->user_id !== $userId) {
            abort(code: 403);
        }

        $this->cepRepository->delete(id: $cep->id, userId: $userId);

        return ['success' => 'CEP deletado com sucesso!'];
    }
}