<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Cep;
use App\Services\ViaCEPService;
use App\Http\Requests\CreateCepRequest;
use App\Http\Requests\CreateMultipleCepsRequest;
use App\Repositories\Interfaces\CepRepositoryInterface;

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
            Log::warning(message: 'Unauthorized access attempt', context: ['user_id' => $userId, 'cep_id' => $cep->id]);
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
            Log::error(message: 'Failed to create CEP', context: ['cep' => $cleanCep, 'response' => $response]);
            return ['error' => $response['error']];
        }

        $data = $response;

        $existingCep = $this->cepRepository->findByColumn(column: 'cep', value: $data['cep'] ?? $request->cep, userId: auth()->id());
        if ($existingCep) {
            Log::info(message: 'CEP already exists', context: ['cep' => $data['cep'], 'user_id' => auth()->id()]);
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

        Log::info(message: 'CEP created successfully', context: ['cep' => $data['cep'], 'user_id' => auth()->id()]);
        $ceps = $data['cep'];
        $emailData = [
            'email' => auth()->user()->email,
            'title' => "ViaCEP CEPs Adicionados",
            'message' => "Os seguintes CEPs foram adicionados com sucesso:\n$ceps",
        ];
        EmailService::sendEmail($emailData);
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
            Log::error(message: 'Failed to create multiple CEPs', context: ['state' => $state, 'city' => $city, 'address' => $address, 'response' => $response]);
            return ['error' => $response['error']];
        }

        $data = $response;

        $ceps = [];
        foreach ($data as $cepData) {
            $existingCep = $this->cepRepository->findByColumn(column: 'cep', value: $cepData['cep'] ?? $request->cep, userId: auth()->id());
            if ($existingCep) {
                Log::info(message: 'CEP already exists', context: ['cep' => $cepData['cep'], 'user_id' => auth()->id()]);
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
            $ceps[] = $cepData['cep'];
        }

        if($ceps){
            Log::info(message: 'Multiple CEPs created successfully', context: ['state' => $state, 'city' => $city, 'address' => $address, 'user_id' => auth()->id()]);
            $cepsString = implode(", ",$ceps);
            $emailData = [
                'email' => auth()->user()->email,
                'title' => "ViaCEP CEPs Adicionados",
                'message' => "Os seguintes CEPs foram adicionados com sucesso:\n$cepsString",
            ];
            EmailService::sendEmail($emailData);
            return ['success' => 'CEPs salvos com sucesso!'];
        }else{
            return ['error' => 'Você já possui estes CEPs salvos!'];
        }
    }

    public function deleteCep(Cep $cep, $userId): array
    {
        if ($cep->user_id !== $userId) {
            Log::warning(message: 'Unauthorized delete attempt', context: ['user_id' => $userId, 'cep_id' => $cep->id]);
            abort(code: 403);
        }

        $this->cepRepository->delete(id: $cep->id, userId: $userId);

        Log::info(message: 'CEP deleted successfully', context: ['cep_id' => $cep->id, 'user_id' => $userId]);
        return ['success' => 'CEP deletado com sucesso!'];
    }
}
