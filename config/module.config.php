<?php

return [
    'service_manager' => [
        'invokables' => [
            mxdiModule\Service\DiFactory::class,
        ],
        'factories' => [
            mxdiModule\Factory\ProxyFactory::class => mxdiModule\Factory\ProxyFactory::class,
        ],
        'abstract_factories' => [
            mxdiModule\Factory\DiAbstractFactory::class,
        ],
    ],
    'mxdimodule' => [
        'proxy_dir'     => 'data/mxdiModule',
        'proxy_namespace' => 'mxdiModuleProxy',
        'cache_adapter' => 'memory',
        'cache_options' => [],
        'avoid_service' => [
            'zendmodulemanagermodulemanager' => true,
            'zendi18ntranslatortranslatorinterface' => true,
        ],
    ],
    'console' => [
        'router' => [
            'routes' => [
                'mxdimodule-proxy-clear' => [
                    'options' => [
                        'route'    => 'mxdimodule proxy clear',
                        'defaults' => [
                            'controller' => 'mxdiModule\Controller\ProxyClear',
                            'action'     => 'index'
                        ],
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            'mxdiModule\Controller\ProxyClear' => mxdiModule\Factory\Controller\ProxyClearControllerFactory::class,
        ],
    ],
];
