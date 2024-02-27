<?php

namespace App\Ldap;

use LdapRecord\Models\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use LdapRecord\Models\Concerns\CanAuthenticate;


class User extends Model implements Authenticable
{
    use CanAuthenticate;

    /**
     * The object classes of the LDAP model.
     *
     * @var array
     */
    public static $objectClasses = [];
    protected $guidKey = 'uuid';


}
