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
];
