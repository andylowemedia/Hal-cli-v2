<?php
namespace App\Repository;

use Interop\Container\ContainerInterface;

class QueueFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $dbAdapter = $container->get('GearmanDbAdapter');
        
        return new Queue($dbAdapter);
    }
}