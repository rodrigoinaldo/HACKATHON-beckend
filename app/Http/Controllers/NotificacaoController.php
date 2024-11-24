<?php

namespace App\Http\Controllers;

use App\Models\Notificacao;
use App\Models\User;
use Illuminate\Http\Request;

class NotificacaoController extends Controller
{

    public function listarNotificacoes($id)
    {
        $user = User::find($id);
        
        $notificacoes = Notificacao::where('usuario_id', $user->id)
            ->orderBy('criado_em', 'desc')
            ->get();

        return response()->json(['notificacoes' => $notificacoes]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Notificacao $notificacao)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notificacao $notificacao)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Notificacao $notificacao)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notificacao $notificacao)
    {
        //
    }
}
