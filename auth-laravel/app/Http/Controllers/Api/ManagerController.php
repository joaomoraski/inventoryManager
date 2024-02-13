<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Manager;
use App\Models\Permission;
use App\Repositories\ManagerRepository;
use Illuminate\Http\Request;

class ManagerController extends Controller
{
    protected $permission;

    public function __construct()
    {
        $this->permission = new Permission();
    }


    public function show($id)
    {
        if (!auth()->user()->hasPermissionTo($this->permission->getFullNameFromPermission("read", "manager"))) {
            return response()->json([
                "message" => "Você não possui permissão para executar essa ação",
            ], 401);
        }
        $manager = new Manager();
        $repository = new ManagerRepository($manager);
        $manager = $repository->findById($id);
        return response()->json([
            "message" => "Localizado com sucesso",
            "data" => $manager
        ]);
    }

    public function update(Request $request, $id)
    {
        if (!auth()->user()->hasPermissionTo($this->permission->getFullNameFromPermission("update", "manager"))) {
            return response()->json([
                "message" => "Você não possui permissão para executar essa ação",
            ], 401);
        }
        $manager = new Manager();
        $managerRepository = new ManagerRepository($manager);
        $data = $request->validate($manager->getRules());
        if ($managerRepository->update($id, $data)) {
            return response()->json([
                "message" => "Atualizado com sucesso",
            ]);
        }

        return response()->json([
            "message" => "Houve um problema para atualiazr o gerente, verifique os dados ou tente novamente.",
        ], 500);
    }
}
