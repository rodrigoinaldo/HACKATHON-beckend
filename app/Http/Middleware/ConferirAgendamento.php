<?php

namespace App\Http\Middleware;

use App\Models\Reserva;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ConferirAgendamento
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
          // Verifica se a reserva já existe para o mesmo ambiente e horário
          $conflict = Reserva::where('ambiente_id', $request->ambiente_id)
          ->where('data_reserva', $request->data_reserva)
          ->where(function ($query) use ($request) {
              $query->whereBetween('hora_inicio', [$request->hora_inicio, $request->hora_fim])
                  ->orWhereBetween('hora_fim', [$request->hora_inicio, $request->hora_fim])
                  ->orWhere(function ($query) use ($request) {
                      $query->where('hora_inicio', '<=', $request->hora_inicio)
                          ->where('hora_fim', '>=', $request->hora_fim);
                  });
          })
          ->exists();

      if ($conflict) {
          return response()->json([
              'error' => 'Já existe uma reserva para o ambiente nesse horário.',
          ], 422);
      }

      return $next($request);
    }
}
