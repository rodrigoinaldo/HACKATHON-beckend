<?php

namespace App\Http\Controllers;

use App\Models\User;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(User::all());
    }

    public function destroy(Response $response, $id)
    {
        $user = User::find($id);
        $user->delete();

        return response()->json(['message' => 'Usuário deletado com sucesso'], 204);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role
        ]);

        return response()->json(['message' => 'Usuário atualizado com sucesso'], 200);
    }

    public function show($id)
    {
        $user = User::find($id);
        return response()->json($user);
    }
}
