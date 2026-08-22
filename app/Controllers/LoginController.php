<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\UsuarioModel;
use Core\Controller;

class LoginController extends Controller
{
    public function index(): void
    {
        $this->viewAuth('login');

    }

    public function autenticar(){
        $usuario = $_POST['nome'] ?? "";
        $senha = $_POST['senha'] ?? "";
        $usuarioModel = new UsuarioModel();
        $usuarioEncontrado = $usuarioModel->autenticar($usuario, $senha);

        if ($usuarioEncontrado) {
            header('Location: /rastreador-ti/public/home');
            exit();
        }

        $this->viewAuth('login', [
            'erro' => 'Usuário ou senha inválido.'
        ]);
    }
    
}
