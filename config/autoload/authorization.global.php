<?php

declare(strict_types=1);

return [
    'dependencies'      => [],
    'dot_authorization' => [
        'guest_role'            => 'guest',
        'role_provider_manager' => [],
        'role_provider'         => [
            'type'    => 'InMemory',
            'options' => [
                'roles' => [
                    'ECM_DEPARTMENT'    => ['permissions' => ['ECM_DEPARTMENT']],
                    'ECM_LEGAL'         => ['permissions' => ['ECM_LEGAL']],
                    'ECM_RISK'          => ['permissions' => ['ECM_RISK']],
                    'ECM_PURCHASING'    => ['permissions' => ['ECM_PURCHASING']],
                    'ECM_VENDOR'        => ['permissions' => ['ECM_VENDOR']],
                    'ECM_MAYOR'         => ['permissions' => ['ECM_MAYOR']],
                    
                    'ECM_IT' => [
                        'permissions' => [
                            'ECM_IT',
                        ],
                    ],
                    'ECM_MO' => [
                        'permissions' => [
                            'ECM_MO',
                        ],
                    ],
                    'user'  => [
                        'permissions' => [
                            'authenticated',
                            'premium',
                            'all'
                        ],
                    ],
                    'guest' => [
                        'permissions' => [
                            'unauthenticated',
                            'all'
                        ],
                    ],
                ],
            ],
        ],
        'assertion_manager'     => [],
        'assertions'            => [],
    ],
];
