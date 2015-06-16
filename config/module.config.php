<?php

return [
    'service_manager' => [
        'invokables' => [
            mxdiModule\Service\DiFactory::class,
        ],
        'abstract_factories' => [
            mxdiModule\Factory\DiAbstractFactory::class,
        ],
    ],
    'mxdimodule' => [
        'cache_adapter' => 'memory',
        'cache_options' => [],
        'avoid_service' => [
            'zendmodulemanagermodulemanager' => true,
            'zendi18ntranslatortranslatorinterface' => true,
        ],
    ],
];
