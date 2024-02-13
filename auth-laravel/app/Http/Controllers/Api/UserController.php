<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateUserRequest;
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
        if (!auth()->user()->hasPermissionTo($this->permission->getFullNameFromPermission("create", "users"))) {
            return response()->json([
                "message" => "Você não possui permissão para executar essa ação",
            ], 401);
        }
        $manager_id = auth()->user()->manager->id;
        $user = new User();
        $userRepository = new UserRepository($user);
        $data = $request->validated();
        $role = $data->role;
        if (($role == "admin" || $role == "owner") and !auth()->user()->hasRole('admin')) {
            return response()->json([
                "message" => "Você não possui permissão para executar essa ação, apenas administradores podem adicionar usuários donos ou administradores.",
            ], 401);
        }
        $data["manager_id"] = $manager_id;
        $user = $userRepository->create($data);

        return response()->json([
            "message" => "Usuário $user->name criado com sucesso",
            "data" => $user
        ]);
    }

    public function listUsers(Request $request)
    {
        if (!auth()->user()->hasPermissionTo($this->permission->getFullNameFromPermission("read", "users"))) {
            return response()->json([
                "message" => "Você não possui permissão para executar essa ação",
            ], 401);
        }
        $userRepository = new UserRepository(auth()->user());
        if ($request->has('fields')) {
            $error = $userRepository->selectWithFields($request->fields);
            if ($error) {
                return response()->json([
                    $error->content()
                ], $error->status());
            }
        }

        try {
            if ($request->has('filters')) {
                $userRepository->selectWithFilter($request->filters);
            }

            if ($request->has('sort')) {
                $sort = explode(',', $request->sort);
                $userRepository->sortBy($sort[0], $sort[1]);
            }
            $userRepository->setWhereManagerIdNotNull();
            $userRepository->setManagerIdFilter();

        } catch (\Exception $e) {
            return response()->json([
                "message" => $e->getMessage()
            ], 500);
        }
        return response()->json([
            "data" => $userRepository->getResults()
        ], 200);
    }

    public function findById(Request $request)
    {
        // TODO Validar se o usuário a ser localizado é do cliente de quem esta localizando
        if (!auth()->user()->hasPermissionTo($this->permission->getFullNameFromPermission("read", "users"))) {
            return response()->json([
                "message" => "Você não possui permissão para executar essa ação",
            ], 401);
        }
        $userRepository = new UserRepository(auth()->user());
        $userRepository->setManagerIdFilter();
        $user = $userRepository->findById($request->id, ["manager"]);
        return response()->json([
            "data" => $user
        ], 200);
    }

    public function update(Request $request, $id)
    {
        if (!auth()->user()->hasPermissionTo($this->permission->getFullNameFromPermission("update", "users"))) {
            return response()->json([
                "message" => "Você não possui permissão para executar essa ação",
            ], 401);
        }
        $user = new User();
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
        if (!auth()->user()->hasPermissionTo($this->permission->getFullNameFromPermission("delete", "users"))) {
            return response()->json([
                "message" => "Você não possuí permissão para realizar esta ação",
            ], 401);
        }
        $user = new User();
        $userRepository = new UserRepository($user);
        $user = $userRepository->findById($id);
        $user->removeRole($user->getRoleNames());
        $isDestroyed = $userRepository->destroy($id);
        $message = $isDestroyed ? "Usuário $user->name excluido com sucesso da base." : "Usuário não pode ser excluido da base.";
        $statusCode = $isDestroyed ? 200 : 500;
        return response()->json([
            "message" => $message
        ], $statusCode);
    }
}

