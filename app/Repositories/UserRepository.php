<?php

namespace App\Repositories;

use App\Models\Manager;

class UserRepository extends AbstractRepository
{
    // Specific logic if necessary

    public function setWhereManagerIdNotNull()
    {
        $this->model = $this->model->whereNotNull("manager_id");
    }

    public function destroy($id)
    {
        $user = $this->findById($id);
        if ($user->hasRole('owner')) {
            $user->removeRole($user->getRoleNames());
            $manager = $user->manager;
            $managerRepository = new ManagerRepository($manager);
            return $managerRepository->destroy($manager->id);
        }
        return $user->delete();
    }
}
