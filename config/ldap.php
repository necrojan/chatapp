<?php

return [
    'ip' => env('LDAP_IP', '3.20.86.205'),
    'port' => env('LDAP_PORT', '389'),
    'prefix' => env('LDAP_PREFIX', 'networkcoverage'),
    'dn' => env('LDAP_DN', 'OU=Users,OU=NetCov,DC=NetworkCoverage,DC=local')
];