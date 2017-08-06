<?php
namespace App\Gearman;

use Interop\Container\ContainerInterface;

class ClientFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $client = new \GearmanClient();
        foreach ($container->get('config')['gearman']['addresses'] as $address) {
            $client->addServer($address);
        }
        
        return $client;
    }
}