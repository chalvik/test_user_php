<?php

declare(strict_types=1);

namespace App\Repository\Interfaces;

use App\Models\User;

interface UserRepositoryInterface
{
    public function insert(User $user): User;
    public function findById(int $id): ?User;
    public function findByLogin(string $login): ?User;
    public function findByEmail(string $email): ?User;
    public function update(User $user): User;
    public function softDelete(int $id): void;
    public function listAll(array $filters = []): array;
}