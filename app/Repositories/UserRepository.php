<?php

namespace App\Repositories;

class UserRepository extends AbstractRepository
{
    // Specific logic if necessary

    public function setWhereManagerIdNotNull()
    {
        $this->model = $this->model->whereNotNull("manager_id");
    }
}
