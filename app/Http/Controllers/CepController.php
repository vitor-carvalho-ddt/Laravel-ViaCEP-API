<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;

use Illuminate\Http\Request;
use App\Models\Cep;
use App\Repositories\Interfaces\CepRepositoryInterface;

class CepController extends Controller
{
    protected $cepRepository;

    public function __construct(CepRepositoryInterface $cepRepository)
    {
        $this->cepRepository = $cepRepository;
    }

    public function index(Request $request)
    {
        $query = $this->cepRepository->allQueryBuilder(auth()->id());

        if ($request->has('search') && $request->has('field')) {
            $search = $request->input('search');
            $field = $request->input('field');
            $query = $query->where($field, 'like', "%{$search}%");
        }

        $ceps = $query->paginate(10);

        return view('ceps.index', compact('ceps'));
    }

    public function show(Cep $cep)
    {
        // Ensure only the owner can view it
        if ($cep->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        return view('ceps.show', compact('cep'));
    }

    public function create()
    {
        return view('ceps.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'cep' => 'required|max:9',
        ]);

        $cleanCep = preg_replace('/\D/', '', $request->cep);

        // Laravel HTTP Client
        $response = Http::get("https://viacep.com.br/ws/{$cleanCep}/json/");
        
        if ($response->failed() || isset($response['erro'])) {
            return back()->withErrors(['cep' => 'CEP not found or invalid.']);
        }

        $data = $response->json();

        // Check if the CEP already exists for the user
        $existingCep = $this->cepRepository->find($data['cep'] ?? $request->cep, auth()->id());
        if ($existingCep) {
            return back()->withErrors(['cep' => 'You already have this CEP saved.']);
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

        return redirect()->route('ceps.index')->with('success', 'CEP salvo com sucesso!');
    }

    public function storeMultiple(Request $request)
    {
        $request->validate([
            'uf' => 'required|max:2',
            'localidade' => 'required|min:3|max:50',
            'logradouro' => 'required|min:3|max:50',
        ]);

        $uf = $request->uf;
        $localidade = $request->localidade;
        $logradouro = $request->logradouro;

        // Laravel HTTP Client
        $response = Http::get("https://viacep.com.br/ws/{$uf}/{$localidade}/{$logradouro}/json/");

        if ($response->failed() || isset($response['erro'])) {
            return back()->withErrors(['cep' => 'Dados inválidos!']);
        }

        $data = $response->json();

        foreach ($data as $cepData) {
            // Check if the CEP already exists for the user
            $existingCep = $this->cepRepository->find($cepData['cep'] ?? $request->cep, auth()->id());
            if ($existingCep) {
                continue; // Skip this CEP if it already exists
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

        return redirect()->route('ceps.index')->with('success', 'CEPs salvos com sucesso!');
    }

    public function destroy(Cep $cep)
    {
        abort_if($cep->user_id !== auth()->id(), 403);
        $this->cepRepository->delete($cep->id, auth()->id());

        return redirect()->route('ceps.index')->with('success', 'CEP deletado com sucesso!');
    }
}