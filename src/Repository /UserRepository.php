<?php

declare(strict_types=1);

namespace App\Repository;

use App\Models\User;
use App\Repository\Interfaces\UserRepositoryInterface;
use PDO;

class UserRepository implements UserRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function insert(User $user): User
    {
        $stmt = $this->pdo->prepare('INSERT INTO users (login, email, created_at) VALUES (:login, :email, :created_at)');
        $stmt->execute([
            ':login' => $user->login,
            ':email' => $user->email,
            ':created_at' => $user->created_at,
        ]);
        $user->id = (int)$this->pdo->lastInsertId();
        return $user;
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new User($row) : null;
    }

    public function findByLogin(string $login): ?User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE login = :login');
        $stmt->execute([':login' => $login]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new User($row) : null;
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new User($row) : null;
    }

    public function update(User $user): User
    {
        $user->updated_at = date('Y-m-d H:i:s');
        $stmt = $this->pdo->prepare('UPDATE users SET login = :login, email = :email, updated_at = :updated_at WHERE id = :id');
        $stmt->execute([
            ':login' => $user->login,
            ':email' => $user->email,
            ':updated_at' => $user->updated_at,
            ':id' => $user->id,
        ]);
        return $user;
    }

    public function softDelete(int $id): void
    {
        $deleted_at = date('Y-m-d H:i:s');
        $stmt = $this->pdo->prepare('UPDATE users SET deleted_at = :deleted_at WHERE id = :id');
        $stmt->execute([':deleted_at' => $deleted_at, ':id' => $id]);
    }

    public function listAll(array $filters = []): array
    {
        $includeDeleted = $filters['include_deleted'] ?? false;
        $sql = $includeDeleted ? 'SELECT * FROM users' : 'SELECT * FROM users WHERE deleted_at IS NULL';
        $stmt = $this->pdo->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($r) => new User($r), $rows);
    }
}