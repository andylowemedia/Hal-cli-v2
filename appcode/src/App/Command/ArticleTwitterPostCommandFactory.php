<?php

namespace App\Command;

use App\Services\SocialMedia\TwitterManager;
use App\Services\SocialMedia\TwitterMessageBuilder;
use Interop\Container\ContainerInterface;
use ZendService\Twitter\Twitter;

class ArticleTwitterPostCommandFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $twitterManager = $container->get(TwitterManager::class);
        $twitterService = $container->get(Twitter::class);
        $twitterMessageBuilder = $container->get(TwitterMessageBuilder::class);

        return new ArticleTwitterPostCommand($twitterManager, $twitterService, $twitterMessageBuilder);
    }
}
