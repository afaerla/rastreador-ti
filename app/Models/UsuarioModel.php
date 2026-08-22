<?php

namespace App\Models;

class UsuarioModel {
    public int $id, $ativo;
    public string $nome, $email, $senha, $perfil, $criado_em, $atualizado_em;

    public function autenticar($usuario, $senha){
        $repository = new UsuarioRepository();
        return $repository->autenticar($usuario, $senha);
        
    }
    
    
}

?>