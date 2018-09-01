<?php
declare(strict_types=1);
namespace App\Services\SocialMedia;


use Interop\Container\ContainerInterface;
use Zend\Db\Sql\Sql;

/**
 * Class TwitterManagerFactory
 * @package App\Services\SocialMedia
 */
class TwitterManagerFactory
{
    /**
     * Invoke factory to return Twitter Manager object
     * @param ContainerInterface $container
     * @return TwitterManager
     */
    public function __invoke(ContainerInterface $container): TwitterManager
    {
        $sql = new Sql($container->get('ArticlesDbAdapter'));

        return new TwitterManager($sql);
    }
}
