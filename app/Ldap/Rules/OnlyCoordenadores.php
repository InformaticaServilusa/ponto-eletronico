<?php

namespace App\Ldap\Rules;

use LdapRecord\Laravel\Auth\Rule;

class OnlyCoordenadores extends Rule
{
    /**
     * Check if the rule passes validation.
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->user->isMemberOf('OU=Users,DC=sci,DC=local');
    }
}
