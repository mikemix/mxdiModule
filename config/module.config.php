<?php

return [
    'service_manager' => [
        'invokables' => [
            mxdiModule\Service\DiFactory::class,
        ],
        'factories' => [
            mxdiModule\Factory\ProxyFactory::class => mxdiModule\Factory\ProxyFactory::class,
            'mxdiModule\Cache'                     => mxdiModule\Factory\Cache\CacheFactory::class,
        ],
        'abstract_factories' => [
            mxdiModule\Factory\DiAbstractFactory::class,
        ],
    ],
    'mxdimodule' => [
        'extractor'         => mxdiModule\Service\YamlExtractor::class,
        'extractor_options' => [],
        'proxy_dir'         => 'data/mxdiModule',
        'proxy_namespace'   => 'mxdiModuleProxy',
        'cache_adapter'     => 'memory',
        'cache_options'     => [],
        'avoid_service'     => [
            'zendmodulemanagermodulemanager' => true,
            'zendi18ntranslatortranslatorinterface' => true,
        ],
    ],
    'console' => [
        'router' => [
            'routes' => [
                'mxdimodule-cache-clear' => [
                    'options' => [
                        'route'    => 'mxdimodule cache clear',
                        'defaults' => [
                            'controller' => 'mxdiModule\Controller\CacheClear',
                            'action'     => 'index'
                        ],
                    ],
                ],
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
            'mxdiModule\Controller\CacheClear' => mxdiModule\Factory\Controller\CacheClearControllerFactory::class,
        ],
    ],
];
