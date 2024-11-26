<?php

namespace App\Http\Controllers;

use App\Models\Ambiente;
use App\Models\Historico;
use App\Models\Notificacao;
use Illuminate\Http\Request;

class AmbienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reuslts = Ambiente::all();
        return response()->json($reuslts);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        try {
            $request->validate([
                'nome' => 'required',
                'tipo' => 'required',
                'status' => 'required|in:reservado,disponivel,manutencao',
                'descricao' => 'required',
            ]);

            $ambiente = Ambiente::create($request->all());

            return response()->json($ambiente, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Ambiente $ambiente)
    {
        return response()->json($ambiente);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ambiente $ambiente)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {

        // Validação dos dados recebidos
        $request->validate([
            'nome' => 'required',
            'tipo' => 'required',
            'status' => 'required|in:reservado,disponivel,manutencao',
            'descricao' => 'required',
        ]);

        // Encontrar o ambiente pelo ID
        $ambiente = Ambiente::find($id);


        if (!$ambiente) {
            return response()->json([
                'message' => 'Ambiente não encontrado.',
            ], 404);
        }


        // Atualizar os campos
        $ambiente->update([
            'nome' => $request->input('nome'),
            'tipo' => $request->input('tipo'), // Atualizando o campo `tipo`
            'descricao' => $request->input('descricao'),
            'status' => $request->input('status'),
        ]);

        
        // Retornar resposta
        return response()->json([
            'message' => 'Ambiente atualizado com sucesso.',
            'ambiente' => $ambiente,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ambiente $ambiente)
    {
        $ambiente->delete();

        return response()->json(['delatado com sucesso', 204]);
    }
}
