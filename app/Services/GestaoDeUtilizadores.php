<?php
namespace App\Services;
use App\Utilizador;
use LdapRecord\Models\ActiveDirectory\User;
class GestaoDeUtilizadores
{
    public function findOrCreateUser($ldapUser, $is_coordenador = false) : Utilizador
    {
        $utilizador_db = Utilizador::where('guuID', $ldapUser->getConvertedGuid())->first();
        if(!$utilizador_db){
            $utilizador_db = new Utilizador();
            $utilizador_db->guuID = $ldapUser->getConvertedGuid();
            $utilizador_db->nome = $ldapUser->getName();
            $utilizador_db->email = $ldapUser->getFirstAttribute('mail');
            $utilizador_db->local = $ldapUser->getFirstAttribute('physicaldeliveryofficename');
            $utilizador_db->departamento = $ldapUser->getFirstAttribute('department');
            $utilizador_db->cargo = $ldapUser->getFirstAttribute('title');
            $coordenador = User::find($ldapUser->getFirstAttribute('manager'));
            if($coordenador->getDn() != "" && $coordenador){
                $coordenador = $this->findOrCreateUser($coordenador, true);
            }
            $utilizador_db->coordenador_id = $coordenador->id;
            if($is_coordenador){
                $utilizador_db->_coodenador = 1;
            }
            $utilizador_db->save();
        }

        return $utilizador_db;
    }
}
