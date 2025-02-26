<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Cep;
use App\Services\CepService;
use App\Http\Requests\CreateCepRequest;
use App\Http\Requests\CreateMultipleCepsRequest;


class CepController extends Controller
{
    protected $cepService;

    public function __construct(CepService $cepService)
    {
        $this->cepService = $cepService;
    }

    public function index(Request $request): View
    {
        $ceps = $this->cepService->getAllCeps(userId: auth()->id(), search: $request->input('search'), field: $request->input(key: 'field'));

        return view(view: 'ceps.index', data: compact(var_name: 'ceps'));
    }

    public function show(Cep $cep): View
    {
        $cep = $this->cepService->getCepDetails(cep: $cep, userId: auth()->id());

        return view(view: 'ceps.show', data: compact(var_name: 'cep'));
    }

    public function create(): View
    {
        return view(view: 'ceps.create');
    }

    public function store(CreateCepRequest $request): RedirectResponse
    {
        $result = $this->cepService->createCep(request: $request);

        if (isset($result['error'])) {
            return back()->withErrors(provider: ['cep' => $result['error']]);
        }

        return redirect()->route(route: 'ceps.index')->with(key: 'success', value: $result['success']);
    }

    public function storeMultiple(CreateMultipleCepsRequest $request): RedirectResponse
    {
        $result = $this->cepService->createMultipleCeps(request: $request);

        if (isset($result['error'])) {
            return back()->withErrors(provider: ['cep' => $result['error']]);
        }

        return redirect()->route(route: 'ceps.index')->with(key: 'success', value: $result['success']);
    }

    public function destroy(Cep $cep): RedirectResponse
    {
        $result = $this->cepService->deleteCep(cep: $cep, userId: auth()->id());

        return redirect()->route(route: 'ceps.index')->with(key: 'success', value: $result['success']);
    }
}