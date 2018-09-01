<?php

namespace AppTest\Services\Aws;

use App\Services\Aws\ClientFactory;
use Aws\Ec2\Ec2Client;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

class ClientFactoryTest extends TestCase
{
    public function testFactoryProducesAwsClient()
    {
        $container = $this->getMockBuilder(ContainerInterface::class)
            ->setMethods(['get'])
            ->getMockForAbstractClass();

        $config = [
            'aws' => [
                'client' => [
                    'region'    => 'eu-west-1',
                    'version'   => '2016-11-15',
                    'profile'   => 'default'
                ]
            ]
        ];

        $container->expects($this->once())->method('get')->with('config')->willReturn($config);

        $awsClient = (new ClientFactory())($container);

        $expectedClient = new Ec2Client($config['aws']['client']);

        $this->assertEquals($expectedClient, $awsClient);
    }
}
