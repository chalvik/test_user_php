<?php

declare(strict_types=1);

namespace App\Validators;

use App\Exceptions\ValidationException;
use App\Models\User;

class UserValidator
{

    public function __construct(
        private array $forbiddenWords = [],
        private array $bannedDomains = []
    )
    {
        $this->forbiddenWords = $forbiddenWords ?: ['admin', 'root', 'support'];
        $this->bannedDomains = $bannedDomains ?: ['spam.example', 'disposable.test'];
    }

    public function validateForCreate(User $user): void
    {
        $this->validateLogin($user->login);
        $this->validateEmail($user->email);
    }

    public function validateForUpdate(User $user): void
    {
        $this->validateForCreate($user);
    }

    private function validateLogin(string $login): void
    {
        if (strlen($login) < 3) {
            throw new \App\ValidationException('Login must be at least 3 characters long');
        }
        if (!preg_match('/^[a-z0-9]+$/', $login)) {
            throw new ValidationException('Login may contain only a-z and 0-9');
        }
        foreach ($this->forbiddenWords as $bad) {
            if (stripos($login, $bad) !== false) {
                throw new ValidationException("Login contains forbidden word: $bad");
            }
        }
    }

    private function validateEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException('Email has invalid format');
        }
        $domain = strtolower(substr(strrchr($email, '@'), 1) ?: '');
        foreach ($this->bannedDomains as $bd) {
            if ($domain === strtolower($bd)) {
                throw new ValidationException('Email domain is not allowed');
            }
        }
    }
}