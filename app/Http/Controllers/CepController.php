<?php

namespace App\Http\Controllers;

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

    public function index(Request $request)
    {
        $ceps = $this->cepService->getAllCeps(auth()->id(), $request->input('search'), $request->input('field'));

        return view('ceps.index', compact('ceps'));
    }

    public function show(Cep $cep)
    {
        $cep = $this->cepService->getCepDetails($cep, auth()->id());

        return view('ceps.show', compact('cep'));
    }

    public function create()
    {
        return view('ceps.create');
    }

    public function store(CreateCepRequest $request)
    {
        $result = $this->cepService->createCep($request);

        if (isset($result['error'])) {
            return back()->withErrors(['cep' => $result['error']]);
        }

        return redirect()->route('ceps.index')->with('success', $result['success']);
    }

    public function storeMultiple(CreateMultipleCepsRequest $request)
    {
        $result = $this->cepService->createMultipleCeps($request);

        if (isset($result['error'])) {
            return back()->withErrors(['cep' => $result['error']]);
        }

        return redirect()->route('ceps.index')->with('success', $result['success']);
    }

    public function destroy(Cep $cep)
    {
        $result = $this->cepService->deleteCep($cep, auth()->id());

        return redirect()->route('ceps.index')->with('success', $result['success']);
    }
}