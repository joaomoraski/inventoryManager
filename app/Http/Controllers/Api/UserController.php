<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\Permission;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Traits\UUID;
use Illuminate\Http\Request;

class UserController extends Controller
{


    protected $permission;
    public function __construct()
    {
        $this->middleware("auth:api");
        $this->permission = new Permission();
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

    public function update(Request $request, $id)
    {
        $user = new User();
        if (!auth()->user()->can($this->permission->getFullNameFromPermission("update", "users"))) {
            return response()->json([
                "message" => "Você não possuí permissão para realizar esta ação",
            ], 401);
        }
        $userRepository = new UserRepository($user);
        $rules = $user->getUpdateRules($id);
        $data = $request->validate($rules);
        if ($userRepository->update($id, $data)) {
            return response()->json([
                "message" => "Usuário foi atualizado com súcesso",
            ]);
        }

        return response()->json([
            "message" => "Opa opa, o usuário teve problemas para ser atualizado, verifica ai.",
        ]);
    }


    public function delete($id)
    {
        $message = "Falha em excluir usuário da base.";
        $user = new User();
        $userRepository = new UserRepository($user);
        $user = $userRepository->findById($id);
        $user->assignRole("stockManager");
        dd(auth()->user()->can($this->permission->getFullNameFromPermission("delete", "users")));
        return $userRepository->destroy($id);
//        if ($destroyed) {
//            $message = "Usuário $user->name, foi excluido da base com sucesso.";
//        }
        return response()->json([
            "message" => $message
        ], 200);
    }

}

