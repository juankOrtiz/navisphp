<?php

namespace Core;

class Authenticator
{
    public function atempt(string $email, string $password): bool
    {
        $user = App::resolve(Database::class)
            ->query(
                "SELECT *
                FROM users
                WHERE email = :email", [
                    'email' => $email
                ]
            )->find();

        if($user) {
            if(password_verify($password, $user['password'])) {
                $this->login([
                    'email' => $email,
                    'type' => $user['type'],
                ]);

                return true;
            }
        }

        return false;
    }

    public function login(array $user): void
    {
        $_SESSION['user'] = [
            'email' => $user['email'],
            'type' => $user['type'],
        ];

        session_regenerate_id(true);
    }

    public function logout(): void
    {
        Session::destroy();
    }
}