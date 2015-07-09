<?php

return [
    'service_manager' => [
        'invokables' => [
            mxdiModule\Service\DiFactory::class,
        ],
        'factories' => [
            mxdiModule\Factory\ProxyFactory::class => mxdiModule\Factory\ProxyFactory::class,
            'mxdiModule\Cache'                     => mxdiModule\Factory\Cache\CacheFactory::class,
            'mxdiModule\Extractor'                 => mxdiModule\Factory\Service\ExtractorFactory::class,
        ],
        'abstract_factories' => [
            mxdiModule\ServiceManager\DiAbstractFactory::class,
        ],
    ],
    'mxdimodule' => [
        'extractor'         => mxdiModule\Service\AnnotationExtractor::class,
        'extractor_options' => [],
        'proxy_dir'         => 'data/mxdiModule',
        'proxy_namespace'   => 'mxdiModuleProxy',
        'cache_adapter'     => 'memory',
        'cache_options'     => [],
        'avoid_service'     => [
            'zendmodulemanagermodulemanager'               => true,
            'zendi18ntranslatortranslatorinterface'        => true,
            'doctrinemoduleformelementobjectselect'        => true,
            'doctrinemoduleformelementobjectradio'         => true,
            'doctrinemoduleformelementobjectmulticheckbox' => true,
            'doctrine.entitymanager.ormdefault'            => true,
            'doctrine.connection.ormdefault'               => true,
            'doctrine.configuration.ormdefault'            => true,
            'doctrine.driver.ormdefault'                   => true,
            'doctrine.cache.array'                         => true,
            'doctrine.eventmanager.ormdefault'             => true,
            'doctrine.entityresolver.ormdefault'           => true,
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
