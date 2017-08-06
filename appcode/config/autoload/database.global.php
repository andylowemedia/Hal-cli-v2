<?php
use Zend\Db\Adapter\AdapterAbstractServiceFactory;

return [
    'dependencies' => [
        'factories' => [
            'GearmanDbAdapter' => AdapterAbstractServiceFactory::class,
            'ArticlesDbAdapter' => AdapterAbstractServiceFactory::class,
        ]
    ],

    'db' => [
        'adapters' => [
            'ArticlesDbAdapter' => [
                'driver'         => 'Pdo_Mysql',
                'options' => [
                    'buffer_results' => true,
                ],
                'driver_options' => [
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
                ],
            ],
            'GearmanDbAdapter' => [
                'driver'         => 'Pdo_Mysql',
                'options' => [
                    'buffer_results' => true,
                ],
                'driver_options' => [
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
                ],
            ],
        ],
    ],
];