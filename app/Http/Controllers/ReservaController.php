<?php

namespace App\Http\Controllers;

use App\Http\Middleware\ConferirAgendamento;
use App\Mail\ReservaAlteradaMail;
use App\Models\Notificacao;
use App\Models\Reserva;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Mockery\Matcher\Not;

class ReservaController extends Controller
{


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reservas = Reserva::with(['ambiente', 'user'])
            ->orderBy('data_reserva', 'asc')
            ->get();

        $formattedReservas = $reservas->map(function ($reserva) {
            return [
                'id' => $reserva->id,
                'data_reserva' => $reserva->data_reserva,
                'hora_inicio' => $reserva->hora_inicio,
                'hora_fim' => $reserva->hora_fim,
                'status' => $reserva->status,
                'ambiente' => $reserva->ambiente->nome ?? 'Ambiente não especificado',
                'usuario' => $reserva->user->name ?? 'Usuário desconhecido',
                'usuario_id' => $reserva->user_id
            ];
        });

        return response()->json([
            'reservas' => $formattedReservas,
        ]);
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

        // dd($request->all());

        $reserva = Reserva::create($request->all());

        Notificacao::create([
            'usuario_id' => $request->user_id,  // Corrigido para 'usuario_id'
            'reservas_id' => $request->ambiente_id,
            'mensagem' => "Sua reserva para o ambiente {$request->ambiente_id} foi criada.",
            'tipo' => 'reserva',
            'criado_em' => now()
        ]);

        //$reserva->load('ambiente', 'user');
        return response()->json(['message' => 'Reserva criada com sucesso.', 'reserva' => $reserva], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Reserva $reserva)
    {
        // Carregar a relação do usuário
        $reserva->load('user', 'ambiente'); // Certifique-se de que as relações existem no modelo Reserva

        // Formatar a resposta
        return response()->json([
            'id' => $reserva->id,
            'data_reserva' => $reserva->data_reserva,
            'hora_inicio' => $reserva->hora_inicio,
            'hora_fim' => $reserva->hora_fim,
            'status' => $reserva->status,
            'usuario' => $reserva->user->name ?? 'Usuário desconhecido',
            'ambiente' => $reserva->ambiente->nome ?? 'Ambiente não especificado',
        ]);
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

        // Validação dos dados de entrada
        // $request->validate([
        //     'ambiente_id' => 'required',
        //     'user_id' => 'required',
        //     'data_reserva' => 'required|date',
        //     'hora_inicio' => 'required|date_format:H:i',
        //     'hora_fim' => 'required|date_format:H:i',
        //     'status' => 'required|in:ativo,cancelado',
        // ]);


        // Atualiza a reserva com os novos dados
        $reserva->update([
            'ambiente_id' => $request->input('ambiente_id'),
            'user_id' => $request->input('user_id'),
            'data_reserva' => $request->input('data_reserva'),
            'hora_inicio' => $request->input('hora_inicio'),
            'hora_fim' => $request->input('hora_fim'),
            'status' => $request->input('status'),
        ]);

        // Resposta com os dados da reserva atualizada
        return response()->json([
            'message' => 'Reserva atualizada com sucesso.',
            'reserva' => $reserva,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reserva $reserva)
    {
        $reserva->delete();

        Notificacao::created([
            'users_id' => $reserva->user_id,
            'mensagem' => "Sua reserva para o ambiente {$reserva->ambiente_id} foi cancelada.",
            'tipo' => 'cancelamento',
            'criado_em' => now()
        ]);

        return response()->json(['message' => 'Reserva deletada com sucesso'], 204);
    }
}
