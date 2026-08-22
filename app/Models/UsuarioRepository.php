<?php

declare(strict_types=1);

namespace App\Models;

use Config\Database;
use PDO;

class UsuarioRepository {
    private PDO $connection;

    public function __construct() {
        $this->connection = Database::getInstance();
    }

    public function autenticar($usuario, $senha) {    
        $sql = "SELECT * FROM usuarios WHERE nome = :usuario";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            'usuario' => $usuario
        ]);

        $usuarioEncontrado = $stmt->fetch();
       

        if ($usuarioEncontrado && password_verify($senha, $usuarioEncontrado['senha_hash'])) {
            $_SESSION['usuario'] = $usuarioEncontrado['id'];
            return true;
        }

        return false;
    }
}
