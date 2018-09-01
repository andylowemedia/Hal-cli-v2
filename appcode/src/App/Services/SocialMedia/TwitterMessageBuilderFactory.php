<?php
declare(strict_types=1);
namespace App\Services\SocialMedia;

use Interop\Container\ContainerInterface;

/**
 * Class TwitterMessageBuilderFactory
 * @package App\Services\SocialMedia
 */
class TwitterMessageBuilderFactory
{
    /**
     * Invoke factory to build Twitter Message Builder object
     * @param ContainerInterface $container
     * @return TwitterMessageBuilder
     */
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get('config');
        return new TwitterMessageBuilder($config['twitter']['yournews365']['baseUrl']);
    }
}
