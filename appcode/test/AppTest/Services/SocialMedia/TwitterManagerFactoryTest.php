<?php

namespace AppTest\Services\SocialMedia;


use App\Services\SocialMedia\TwitterManager;
use App\Services\SocialMedia\TwitterManagerFactory;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;
use Zend\Db\Adapter\AdapterInterface;

class TwitterManagerFactoryTest extends TestCase
{
    public function testInstanceBuiltIsTwitterManager()
    {
        $adapter = $this->getMockBuilder(AdapterInterface::class)->getMockForAbstractClass();

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->setMethods(['get'])
            ->getMockForAbstractClass();

        $container->expects($this->once())
            ->method('get')
            ->willReturn($adapter);

        $twitterManager = (new TwitterManagerFactory)($container);

        $this->assertInstanceOf(TwitterManager::class, $twitterManager);
    }
}
