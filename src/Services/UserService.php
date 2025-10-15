<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Repository\Interfaces\UserRepositoryInterface;
use App\Validators\UserValidator;
use App\Exceptions\ValidationException;

class UserService
{
    public function __construct(
        private UserRepositoryInterface $repo,
        private UserValidator $validator,
        private ?LoggerInterface $logger = null)
    {}

    public function create(User $user, ?int $actorId = null): User
    {
        $this->validator->validateForCreate($user);
        if ($this->repo->findByLogin($user->login)) {
            throw new ValidationException('Login already exists');
        }
        if ($this->repo->findByEmail($user->email)) {
            throw new ValidationException('Email already exists');
        }

        try {
            $created = $this->repo->insert($user);
            $this->logger?->info('User created', ['id' => $created->id]);
            return $created;
        } catch (PDOException $e) {
            $this->logger?->error('DB error on create', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function update(User $user, ?int $actorId = null): User
    {
        $existing = $this->repo->findById($user->id);
        if (!$existing) throw new \RuntimeException('User not found');
        $this->validator->validateForUpdate($user);

        if ($user->login !== $existing->login && $this->repo->findByLogin($user->login)) throw new ValidationException('Login already exists');
        if ($user->email !== $existing->email && $this->repo->findByEmail($user->email)) throw new ValidationException('Email already exists');

        $old = $existing->toArray();
        $updated = $this->repo->update($user);
        $this->audit->log($updated->id, $actorId, 'update', $old, $updated->toArray());
        $this->logger?->info('User updated', ['id' => $updated->id]);
        return $updated;
    }

    public function delete(int $id, ?int $actorId = null): void
    {
        $existing = $this->repo->findById($id);
        if (!$existing) throw new \RuntimeException('User not found');
        if ($existing->deleted_at) return;

        $this->repo->softDelete($id);
        $after = $this->repo->findById($id);
        $this->logger?->info('User soft-deleted', ['id' => $id]);
    }

    public function list(array $filters = []): array
    {
        return $this->repo->listAll($filters);
    }

    public function findById(int $id): ?User
    {
        return $this->repo->findById($id);
    }
}