<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    protected $model;

    /**
     * UserRepository constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->model = $user;
    }

    /**
     * @param array $inputs
     * @return mixed
     */
    public function create(array $inputs)
    {
        return $this->model->create($inputs);
    }
}