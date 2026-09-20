<?php

declare(strict_types=1);

namespace Core;

class Csrf
{
    private const SESSION_KEY = 'csrf_token';

    public static function token(): string
    {
        if (empty($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::SESSION_KEY];
    }

    public static function validar(): void
    {
        $enviado = (string) ($_POST['csrf'] ?? '');
        $esperado = (string) ($_SESSION[self::SESSION_KEY] ?? '');

        if ($esperado === '' || !hash_equals($esperado, $enviado)) {
            http_response_code(419);
            echo '<h1>Erro 419</h1>';
            echo '<p>Sessão expirada ou token de segurança inválido. Volte e tente novamente.</p>';
            exit;
        }
    }
}
