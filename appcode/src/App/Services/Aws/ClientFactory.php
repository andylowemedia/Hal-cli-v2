<?php
declare(strict_types=1);
namespace App\Services\Aws;

use Aws\Ec2\Ec2Client;
use Interop\Container\ContainerInterface;

/**
 * Class ClientFactory
 * @package App\Services\Aws
 */
class ClientFactory
{
    /**
     * Invoke factory for EC2 Client
     * @param ContainerInterface $container
     * @return Ec2Client
     */
    public function __invoke(ContainerInterface $container): Ec2Client
    {
        $awsClientConfig = $container->get('config')['aws']['client'];
        
        return new Ec2Client($awsClientConfig);
    }
}
