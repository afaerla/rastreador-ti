<?php

declare(strict_types=1);

namespace App\Models;

class UsuarioModel
{
    /** @return array{id: int, nome: string, email: string, perfil: string}|null */
    public function autenticar(string $email, string $senha): ?array
    {
        return (new UsuarioRepository())->autenticar($email, $senha);
    }
}
