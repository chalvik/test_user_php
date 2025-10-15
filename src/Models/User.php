<?php

declare(strict_types=1);

namespace App\Models;

final class User
{
    public function __construct(
        ?int $id,
        string $login,
        string $email,
        string $created_at,
        string $updated_at,
        string $deleted_a
        ) {}
}