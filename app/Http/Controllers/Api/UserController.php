<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateUserRequest;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class UserController extends Controller
{


    public function __construct()
    {
        $this->middleware("auth:api");
    }

    public function create(CreateUserRequest $request)
    {
        // TODO: Validar usuário adminsitrador
        $manager_id = auth()->user()->manager->id;
        $user = new User();
        $userRepository = new UserRepository($user);
        $data = $request->validated();
        $data["manager_id"] = $manager_id;
        $user = $userRepository->create($data);

        return response()->json([
            "message" => "Usuário $user->name criado com súcesso",
            "data" => $user
        ]);
    }

    public function listUsers(Request $request)
    {
        $userRepository = new UserRepository(auth()->user());
        if ($request->has('fields')) {
            $userRepository->selectWithFields($request->fields);
        }

        if ($request->has('filters')) {
            $userRepository->selectWithFilter($request->filters);
        }

        if ($request->has('sort')) {
            $sort = explode(',', $request->sort);
            $userRepository->sortBy($sort[0], $sort[1]);
        }
        $userRepository->setWhereManagerIdNotNull();
        $userRepository->setManagerIdFilter();

        return response()->json([
            "data" => $userRepository->getResults()
        ], 200);
    }

    public function findById(Request $request)
    {
        // TODO Validar se o usuário a ser localizado é do cliente de quem esta localizando
        $userRepository = new UserRepository(auth()->user());
        $user = $userRepository->findById($request->id, ["manager"]);
        return response()->json([
            "data" => $user
        ], 200);
    }

    public function update(Request $request)
    {

    }


    public function delete(Request $request)
    {

    }

}

