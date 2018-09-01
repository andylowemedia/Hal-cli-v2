<?php

use App\Services\SocialMedia\TwitterManager;
use App\Services\SocialMedia\TwitterManagerFactory;
use ZendService\Twitter\Twitter;
use App\Services\SocialMedia\ZendServiceTwitterFactory;
use App\Services\SocialMedia\TwitterMessageBuilderFactory;
use App\Services\SocialMedia\TwitterMessageBuilder;
use Zend\Db\Adapter\AdapterAbstractServiceFactory;
use App\Command\ArticleTwitterPostCommand;
use App\Command\ArticleTwitterPostCommandFactory;

return [
    'dependencies' => [
        'factories' => [
            TwitterManager::class => TwitterManagerFactory::class,
            Twitter::class => ZendServiceTwitterFactory::class,
            TwitterMessageBuilder::class => TwitterMessageBuilderFactory::class,
            'ArticlesDbAdapter' => AdapterAbstractServiceFactory::class,
            ArticleTwitterPostCommand::class => ArticleTwitterPostCommandFactory::class
        ],
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
                'hostname' => getenv('DB_ARTICLES_HOST'),
                'database' => getenv('DB_ARTICLES_SCHEMA'),
                'username' => getenv('DB_ARTICLES_USER'),
                'password' => getenv('DB_ARTICLES_PASSWORD'),
            ],
        ],
    ],
    'twitter' => [
        'yournews365' => [
            'apiKey' => getenv('TWITTER_API_KEY'),
            'apiSecret' => getenv('TWITTER_API_SECRET'),
            'token' => getenv('TWITTER_TOKEN'),
            'secret' => getenv('TWITTER_SECRET'),
            'baseUrl' => getenv('TWITTER_MESSAGE_BASE_URL'),
        ]
    ],
    'commands' => [
        ArticleTwitterPostCommand::class,
    ]

];
