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
                    
                    'ECM_AC' => ['permissions' => ['ECM_AC']],
                    'ECM_BD' => ['permissions' => ['ECM_BD']],
                    'ECM_BE' => ['permissions' => ['ECM_BE']],
                    'ECM_CC' => ['permissions' => ['ECM_CC']],
                    'ECM_CN' => ['permissions' => ['ECM_CN']],
                    'ECM_ED' => ['permissions' => ['ECM_ED']],
                    'ECM_EG' => ['permissions' => ['ECM_EG']],
                    'ECM_EM' => ['permissions' => ['ECM_EM']],
                    'ECM_EO' => ['permissions' => ['ECM_EO']],
                    'ECM_FD' => ['permissions' => ['ECM_FD']],
                    'ECM_FN' => ['permissions' => ['ECM_FN']],
                    'ECM_GC' => ['permissions' => ['ECM_GC']],
                    'ECM_HD' => ['permissions' => ['ECM_HD']],
                    'ECM_HR' => ['permissions' => ['ECM_HR']],
                    'ECM_IT' => ['permissions' => ['ECM_IT']],
                    'ECM_LU' => ['permissions' => ['ECM_LU']],
                    'ECM_MO' => ['permissions' => ['ECM_MO']],
                    'ECM_PA' => ['permissions' => ['ECM_PA']],
                    'ECM_PD' => ['permissions' => ['ECM_PD']],
                    'ECM_PK' => ['permissions' => ['ECM_PK']],
                    'ECM_PU' => ['permissions' => ['ECM_PU']],
                    'ECM_PW' => ['permissions' => ['ECM_PW']],
                    'ECM_PY' => ['permissions' => ['ECM_PY']],
                    'ECM_PZ' => ['permissions' => ['ECM_PZ']],
                    'ECM_RC' => ['permissions' => ['ECM_RC']],
                    'ECM_RL' => ['permissions' => ['ECM_RL']],
                    'ECM_RM' => ['permissions' => ['ECM_RM']],
                    'ECM_RV' => ['permissions' => ['ECM_RV']],
                    'ECM_SC' => ['permissions' => ['ECM_SC']],
                    'ECM_SD' => ['permissions' => ['ECM_SD']],
                    'ECM_TA' => ['permissions' => ['ECM_TA']],
                    'ECM_TC' => ['permissions' => ['ECM_TC']],
                    'ECM_TX' => ['permissions' => ['ECM_TX']],
                    'ECM_WS' => ['permissions' => ['ECM_WS']],
                    'ECM_YS' => ['permissions' => ['ECM_YS']],
                    
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
