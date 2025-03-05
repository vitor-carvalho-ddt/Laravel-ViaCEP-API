<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Cep;
use App\Services\CepService;
use App\Http\Requests\CreateCepRequest;
use App\Http\Requests\CreateMultipleCepsRequest;


class CepController extends Controller
{
    public function __construct(private readonly CepService $cepService)
    {
    }

    public function index(Request $request): View
    {
        Log::info(message: 'Fetching all CEPs', context: ['user_id' => auth()->id()]);
        $ceps = $this->cepService->getAllCeps(userId: auth()->id(), search: $request->input(key: 'search'), field: $request->input(key: 'field'));

        return view(view: 'ceps.index', data: compact(var_name: 'ceps'));
    }

    public function show(Cep $cep): View
    {
        Log::info(message: 'Fetching CEP details', context: ['user_id' => auth()->id(), 'cep_id' => $cep->id]);
        $cep = $this->cepService->getCepDetails(cep: $cep, userId: auth()->id());

        return view(view: 'ceps.show', data: compact(var_name: 'cep'));
    }

    public function create(): View
    {
        Log::info(message: 'Displaying create CEP form', context: ['user_id' => auth()->id()]);
        return view(view: 'ceps.create');
    }

    public function store(CreateCepRequest $request): RedirectResponse
    {
        Log::info(message: 'Storing new CEP', context: ['user_id' => auth()->id(), 'cep' => $request->cep]);
        $result = $this->cepService->createCep(request: $request);

        if (isset($result['error'])) {
            return back()->withErrors(provider: ['cep' => $result['error']]);
        }

        Log::info(message: 'CEP stored successfully', context: ['user_id' => auth()->id(), 'cep' => $request->cep]);
        return redirect()->route(route: 'ceps.index')->with(key: 'success', value: $result['success']);
    }

    public function storeMultiple(CreateMultipleCepsRequest $request): RedirectResponse
    {
        Log::info(message: 'Storing multiple CEPs', context: ['user_id' => auth()->id(), 'uf' => $request->uf, 'localidade' => $request->localidade, 'logradouro' => $request->logradouro]);
        $result = $this->cepService->createMultipleCeps(request: $request);

        if (isset($result['error'])) {
            Log::error(message: 'Failed to store multiple CEPs', context: ['user_id' => auth()->id(), 'uf' => $request->uf, 'localidade' => $request->localidade, 'logradouro' => $request->logradouro, 'error' => $result['error']]);
            return back()->withErrors(provider: ['cep' => $result['error']]);
        }

        Log::info(message: 'Multiple CEPs stored successfully', context: ['user_id' => auth()->id(), 'uf' => $request->uf, 'localidade' => $request->localidade, 'logradouro' => $request->logradouro]);
        return redirect()->route(route: 'ceps.index')->with(key: 'success', value: $result['success']);
    }

    public function destroy(Cep $cep): RedirectResponse
    {
        Log::info(message: 'Deleting CEP', context: ['user_id' => auth()->id(), 'cep_id' => $cep->id]);
        $result = $this->cepService->deleteCep(cep: $cep, userId: auth()->id());

        Log::info(message: 'CEP deleted successfully', context: ['user_id' => auth()->id(), 'cep_id' => $cep->id]);
        return redirect()->route(route: 'ceps.index')->with(key: 'success', value: $result['success']);
    }
}