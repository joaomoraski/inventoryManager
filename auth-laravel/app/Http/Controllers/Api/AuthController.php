<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRegisterRequest;
use App\Models\Manager;
use App\Models\User;
use App\Repositories\ManagerRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(UserRegisterRequest $request)
    {
        $data = $request->validated();

        $manager = $this->createManager([
            "name" => $data['name'],
            "email" => $data['email'],
            "typeIdentificationNumber" => isset($data['cpf']) ? 2 : 0, // CPF
            "identificationNumber" => $data['cpf'] ?? "",
            "address" => $data['address'] ?? "",
            "addressNumber" => $data['addressNumber'] ?? "",
            "telephone" => $data['telephone'] ?? "",
            "postalCode" => $data['postalCode'] ?? "",
        ]);

        $user = $this->createUser([
            "name" => $data['name'],
            "email" => $data['email'],
            "manager_id" => $manager->id,
            "password" => Hash::make($data['password']),
            "cpf" => $data['cpf'] ?? "",
            "rg" => $data['rg'] ?? "",
            "address" => $data['address'] ?? "",
            "addressNumber" => $data['addressNumber'] ?? "",
            "telephone" => $data['telephone'] ?? "",
            "postalCode" => $data['postalCode'] ?? "",
            "salary" => $data['salary'] ?? 0,
        ]);


        $user->assignRole("owner");

        // Resposta
        return response()->json([
            "status" => true,
            "message" => "Usuário $user->name cadastrado com sucesso!"
        ]);
    }

    private function createManager(array $data)
    {
        $manager = new Manager();
        $managerRepository = new ManagerRepository($manager);
        return $managerRepository->create($data);
    }
    private function createUser(array $data)
    {
        $user = new User();
        $userRepository = new UserRepository($user);
        $user = $userRepository->create($data);
        $user->assignRole("owner");
        return $user;
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
            "message" => "Usuário deslogado com sucesso!"
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
                "message" => "Token não é valido"
            ], 401);
        }
    }
}
