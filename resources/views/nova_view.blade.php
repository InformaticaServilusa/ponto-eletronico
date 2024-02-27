<?php

    use LdapRecord\Container;
    use LdapRecord\Connection;
    use LdapRecord\Models\Entry;



    echo "<h1>Welcome " . $user . "</h1>";
    echo "<pre>".print_r($user,TRUE)."</pre>";
//     create new connection
//     $connection = new Connection ([
//         'hosts' => ['sci.local'],
//         'port' => 389,
//         'base_dn' => 'dc=sci,dc=local',
//         'username' => 'AADConnect@servilusa.pt',
//         'password' => 'IT4cloud#',
// ]);

//     $connection->connect();
//     if($connection){
//         echo "Connected!\n";
//     } else {
//         echo "Not Connected!\n";
//     }

//     $user = $connection->query()->where('samaccountname', 'bcarvalheiro')->firstOrFail();
//     var_dump($user['userprincipalname'][0]);
//     die();
//     if($connection->auth()->attempt($user['userprincipalname'][0], 'Ma@_!9322913_!')){
//         echo "Authenticated!";
//     } else {
//         echo "Not Authenticated!";
//     }


