<?php

namespace App\Http\Controllers;

use App\Http\Middleware\ConferirAgendamento;
use App\Models\Reserva;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reservas = Reserva::all();
        return response()->json($reservas);
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
        $request->validate([
            'ambiente_id' => 'required|exists:ambientes,id',
            'user_id' => 'required|exists:users,id',
            'data_reserva' => 'required|date',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fim' => 'required|date_format:H:i',
            'status' => 'required|in:ativo,cancelado',
        ]);

        $reserva = Reserva::create($request->all());

        $reserva->load('ambiente', 'user');

        return response()->json([
            'message' => 'Reserva criada com sucesso.',
            'reserva' => [
                'id' => $reserva->id,
                'data_reserva' => $reserva->data_reserva,
                'hora_inicio' => $reserva->hora_inicio,
                'hora_fim' => $reserva->hora_fim,
                'status' => $reserva->status,
                'ambiente' => $reserva->ambiente->nome, // Nome do ambiente
                'usuario' => $reserva->user->name, // Nome do usuário
            ]
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Reserva $reserva)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reserva $reserva)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reserva $reserva)
    {
        $request->validate([
            'ambiente_id' => 'required|exists:ambientes,id',
            'user_id' => 'required|exists:users,id',
            'data_reserva' => 'required|date',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fim' => 'required|date_format:H:i',
            'status' => 'required|in:ativo,cancelado',
        ]);

        $reserva->update($request->all());

        return response()->json([
            'message' => 'Reserva atualizada com sucesso.',
            'reserva' => [
                'id' => $reserva->id,
                'data_reserva' => $reserva->data_reserva,
                'hora_inicio' => $reserva->hora_inicio,
                'hora_fim' => $reserva->hora_fim,
                'status' => $reserva->status,
                'ambiente' => $reserva->ambiente->nome, // Nome do ambiente
                'usuario' => $reserva->user->name, // Nome do usuário
            ]
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reserva $reserva)
    {
        $reserva->delete();

        return response()->json(['message' => 'Reserva deletada com sucesso'], 204);
    }
}