<?php
namespace App\Services;
use App\Utilizador;
use LdapRecord\Models\ActiveDirectory\User;

//      TODO: Preciso ainda de fazer documentaçõ desta class
//      TODO:Precis de fazer testes unitários
//      TODO:Preciso de fazer testes de integração
//      TODO:Preciso de fazer testes de aceitação
//      TODO:Preciso de fazer testes de sistema
//      TODO:Preciso de fazer testes de regressão
//      TODO:Se ja existir, verifica se precisa de ser atualizado. Ver o que eh mais susceptivel de mudar.
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
