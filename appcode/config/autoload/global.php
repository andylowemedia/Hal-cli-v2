<?php
return [
    'dependencies' => [
        'factories' => [
            'App\Repository\Queue'              => App\Repository\QueueFactory::class,
            'AwsClient'                         => App\Aws\ClientFactory::class,
            App\Server\ScrapeInstances::class   => App\Server\ScrapeInstancesFactory::class,
            'GearmanClient'                     => App\Gearman\ClientFactory::class,
            'GearmanWorker'                     => App\Gearman\WorkerFactory::class,
        ],
    ],
    'aws' => [
        'client' => [
            'region'    => 'eu-west-1',
            'version'   => '2016-11-15',
            'profile'   => 'default'
        ]
    ]
];