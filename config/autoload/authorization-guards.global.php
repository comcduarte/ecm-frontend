<?php

declare(strict_types=1);

use Dot\Rbac\Guard\Guard\GuardInterface;

return [
    'dot_authorization' => [
        'protection_policy'       => GuardInterface::POLICY_DENY,
        'event_listeners'         => [],
        'guards_provider_manager' => [],
        'guard_manager'           => [],
        'guards_provider'         => [
            'type'    => 'ArrayGuards',
            'options' => [
                'guards' => [
                    [
                        'type' => 'Route',
                        'options' => [
                            'rules' => [
                                'page' => ['*'],
                                'index' => ['*'],
                            ],
                        ],
                    ],
                    [
                        'type' => 'Controller',
                        'options' => [
                            'rules' => [
                                [
                                    'route' => 'user',
                                    //list of actions to apply, or empty array for all actions
                                    'actions' => [
                                        'login','logout','register'
                                    ],
                                    //by default, authorization passes if all permissions are present (AND)
                                    //list of roles to allow
                                    'roles' => ['*'],
                                ],
                                [
                                    'route' => 'home',
                                    'actions' => ['dashboard'],
                                    'roles' => ['*'],
                                ],
                                [
                                    'route' => 'contract::*',
                                    'roles' => ['user'],
                                ],
                                [
                                    'route' => 'route::*',
                                    'roles' => ['ECM_DEPARTMENT'],
                                ],
                                [
                                    'route' => 'workflow::dashboard',
                                    'roles' => ['user'],
                                ],
                                [
                                    'route' => 'document::*',
                                    'roles' => ['*'],
                                ],
                                [
                                    'route' => 'dashboard',
                                    'roles' => ['user'],
                                ],
                                [
                                    'route' => 'amendment::*',
                                    'roles' => ['user'],
                                ],
                                [
                                    'route' => 'cc::*',
                                    'roles' => ['user'],
                                ],
                                [
                                    'route' => 'account',
                                    'roles' => ['user'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
