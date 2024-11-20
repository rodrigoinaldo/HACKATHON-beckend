<?php

namespace App\Http\Controllers;

use App\Models\Ambiente;
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
        //
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
    public function update(Request $request, Ambiente $ambiente)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'tipo' => 'required|string|max:255',
            'status' => 'required|in:reservado,disponivel,manutencao',
            'descricao' => 'required|string|max:1000',
        ]);

        $request->update($request->all());

        return response()->json($request, 200);
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
