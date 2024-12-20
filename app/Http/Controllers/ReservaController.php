<?php

namespace App\Http\Controllers;

use App\Http\Middleware\ConferirAgendamento;
use App\Mail\ReservaAlteradaMail;
use App\Models\Historico;
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

        // Criar histórico de criação da reserva
        Historico::create([
            'usuario_id' => $request->user_id,
            'reserva_id' => $reserva->id,
            'alteracoes' => "Reserva criada para o ambiente {$request->ambiente_id}.",
            'modificado_em' => now(),
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
        $validatedData = $request->validate([
            'ambiente_id' => 'required',
            'user_id' => 'required',
            'data_reserva' => 'required|date',
            'hora_inicio' => 'required|date_format:H:i:s',
            'hora_fim' => 'required|date_format:H:i:s',
            'status' => 'required|in:ativo,cancelado',
        ]);

        // Criação da notificação
        Notificacao::create([
            'usuario_id' => $validatedData['user_id'],
            'reservas_id' => $validatedData['ambiente_id'],
            'mensagem' => "Sua reserva para o ambiente {$validatedData['ambiente_id']} foi alterada.",
            'tipo' => 'lembrete',
            'criado_em' => now()
        ]);

        // Registro no histórico
        Historico::create([
            'usuario_id' => $validatedData['user_id'],
            'reserva_id' => $reserva->id,
            'alteracoes' => "Reserva criada para o ambiente {$validatedData['ambiente_id']}.",
            'modificado_em' => now(),
        ]);

        // Atualiza a reserva com os novos dados
        $reserva->update($validatedData);

        // Resposta com os dados da reserva atualizada
        return response()->json([
            'message' => 'Reserva atualizada com sucesso.',
            'reserva' => $reserva,
        ], 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reserva $reserva, $id)
    {
        // Cria a notificação
        Notificacao::create([
            'usuario_id' => $id,
            'mensagem' => "Sua reserva para o ambiente {$reserva->ambiente_id} foi cancelada.",
            'tipo' => 'cancelamento',
            'criado_em' => now()
        ]);


        // Exclui a reserva
        $reserva->delete();

        // Retorna a resposta
        return response()->json(['message' => 'Reserva deletada com sucesso'], 204);
    }
}
