<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\UserService;

final class UserController
{
    public function __construct(
        private UserService $userService
    ) {}

    public function index()
    {
        return $this->userService->list();
    }

    public function one()
    {
        return $this->userService->findById(1);
    }

    public function create()
    {

    }

    public function update()
    {

    }

    public function delete()
    {
        return $this->userService->delete();
    }
}