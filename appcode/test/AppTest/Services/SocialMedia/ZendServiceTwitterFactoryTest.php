<?php

namespace AppTest\Services\SocialMedia;


use App\Services\SocialMedia\ZendServiceTwitterFactory;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;
use ZendService\Twitter\Twitter;

class ZendServiceTwitterFactoryTest extends TestCase
{
    public function testTwitterObjectIsBuiltCorrectly()
    {
        $container = $this->getMockBuilder(ContainerInterface::class)
            ->setMethods(['get'])
            ->getMockForAbstractClass();

        $config = [
            'twitter' => array(
                'yournews365' => array(
                    'apiKey' => 'rYkAIfSIcZbS2L0n1OY59qVIe',
                    'apiSecret' => 'pC9Q3XhpnmTGueFjEoUkEGoCFFKVLYTHc4Ajpk9y71W5Im1FCE',
                    'token' => '739152618233159680-CmgH6e9eojQ2W1i9DlHhv1lLk7D0I9s',
                    'secret' => 'wsPfSmaGGsciqAxJfJtiWoNhRPCfiUxw60uhB6KI5evro'
                )
            ),
        ];

        $container->expects($this->once())->method('get')->with('config')->willReturn($config);

        $twitter = (new ZendServiceTwitterFactory())($container);

        $this->assertInstanceOf(Twitter::class, $twitter);
    }

}
