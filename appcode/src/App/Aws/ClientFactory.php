<?php
namespace App\Aws;

use Aws\Ec2\Ec2Client;
use Interop\Container\ContainerInterface;

class ClientFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $awsClientConfig = $container->get('config')['aws']['client'];
        
        return new Ec2Client($awsClientConfig);
    }
}