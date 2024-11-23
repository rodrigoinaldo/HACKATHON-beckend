<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'email' => 'nullable|string|email|max:255',
            'password' => 'required|string|min:8',
            'role' => 'nullable'
        ]);

        $role = $request->role;

        if (empty($role) || $role == 'null') {
            $role = 'estudante';
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Usuário cadastrado com sucesso.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
            'role' => $role
        ], 201);
    }


    public function  login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|     ',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciais inválidas.'], 401);
        }

        $token = $user->createToken('auth_token', ['*'])->plainTextToken;

        return response()->json([
            'message' => 'Usuário logado com sucesso.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user->id,
            'role' => $user->role
        ], 200);
    }
}
