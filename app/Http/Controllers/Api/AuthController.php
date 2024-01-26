<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Manager;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            "name" => "required",
            "email" => "required|email|unique:users",
            "password" => "required|confirmed",
            "cpf" => "required|max:20",
            "rg" => "max:20",
            "address" => "min:10|max:200",
            "addressNumber" => "max:10",
            "telephone" => "required|max:20",
            "postalCode" => "max:20",
            "salary" => "numeric"
        ]);

        $manager = Manager::create([
            "name" => $request->name,
            "email" => $request->email,
            "typeIdentificationNumber" => $request->exists("cpf") ? 2 : 0, // CPF
            "identificationNumber" => $request->input("cpf", ""),
            "address" => $request->input("address", ""),
            "addressNumber" => $request->input("addressNumber", ""),
            "telephone" => $request->input("telephone", ""),
            "postalCode" => $request->input("postalCode", ""),
        ]);

        // Cria o usuário
        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "manager_id" => $manager->id,
            "password" => Hash::make($request->password),
            "cpf" => $request->input("cpf", ""),
            "rg" => $request->input("rg", ""),
            "address" => $request->input("address", ""),
            "addressNumber" => $request->input("addressNumber", ""),
            "telephone" => $request->input("telephone", ""),
            "postalCode" => $request->input("postalCode", ""),
            "salary" => $request->input("salary", "")
        ]);

        $user->assignRole("owner");

        // Resposta
        return response()->json([
            "status" => true,
            "message" => "Usuário $user->name cadastrado com sucesso!"
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            "email" => "required|email",
            "password" => "required",
        ]);

        $token = JWTAuth::attempt([
            "email" => $request->email,
            "password" => $request->password
        ]);

        if (empty($token)) {
            return response()->json([
                "status" => false,
                "message" => "E-mail e senha podem estar errados, verifique novamente."
            ]);
        }

        return response()->json([
            "status" => true,
            "message" => "Usuário autenticado com sucesso!.",
            "token" => $token
        ]);
    }

    public function profile()
    {
        $userdata = auth()->user();

        return response()->json([
            "status" => true,
            "message" => "Dados do usuário",
            "data" => $userdata,
            "expires_in" => $this->guard()->factory()->getTTL() * 60
        ]);
    }

    public function guard()
    {
        return Auth::guard();
    }

    public function refreshToken()
    {
//        if (auth()->user()->can('create_stock')) {
//            return response()->json([
//                "status" => false,
//                "message" => "wadawadaweeuuu"
//            ]);
//        }
        $newToken = auth()->refresh();

        return response()->json([
            "status" => true,
            "message" => "Novo token de acesso.",
            "token" => $newToken
        ]);
    }

    public function logout()
    {
        auth()->logout();

        return response()->json([
            "status" => true,
            "message" => "Usuário logado com sucesso!"
        ]);
    }


    public function validateToken(Request $request)
    {
        try {
            $user = auth()->userOrFail();

            return response()->json([
                "status" => true,
                "message" => "Token valido",
                "data" => $user
            ]);
        } catch (UserNotDefinedException $e) {
            return response()->json([
                "status" => false,
                "message" => "Token is not valid"
            ], 401);
        }
    }
}
