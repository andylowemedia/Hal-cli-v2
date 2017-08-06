<?php
namespace App\Server;

use Interop\Container\ContainerInterface;

class ScrapeInstancesFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get('config')['aws']['ec2-instances-setup']['scrape'];
        
        $awsClient = $container->get('AwsClient');
        
        $queueRepository = $container->get(\App\Repository\Queue::class);
        
        return new ScrapeInstances($awsClient, $queueRepository, $config);
    }
}