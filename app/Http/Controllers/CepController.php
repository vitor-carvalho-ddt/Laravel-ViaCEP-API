<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;


use Illuminate\Http\Request;
use App\Models\Cep;

class CepController extends Controller
{

    public function index()
    {
        // Retrieve CEPs for the logged-in user only
        $ceps = Cep::where('user_id', auth()->id())->get();

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

        $cep = new Cep([
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

        $cep->save();

        return redirect()->route('ceps.index')->with('success', 'CEP salvo com sucesso!');
    }

    public function destroy(Cep $cep)
    {
        abort_if($cep->user_id !== auth()->id(), 403);
        $cep->delete();

        return redirect()->route('ceps.index')->with('success', 'CEP deletado com sucesso!');
    }

}
