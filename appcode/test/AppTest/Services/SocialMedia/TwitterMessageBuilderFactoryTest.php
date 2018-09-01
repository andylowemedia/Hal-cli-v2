<?php

namespace AppTest\Services\SocialMedia;


use App\Services\SocialMedia\TwitterMessageBuilder;
use App\Services\SocialMedia\TwitterMessageBuilderFactory;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

class TwitterMessageBuilderFactoryTest extends TestCase
{
    public function testInstanceOfTwitterMessageBuilderIsCreated()
    {
        $container = $this->getMockBuilder(ContainerInterface::class)
            ->setMethods(['get'])
            ->getMockForAbstractClass();

        $config = [
            'twitter' => [
                'yournews365' => [
                    'baseUrl' => 'https://www.yournews365.com/'
                ]
            ],
        ];

        $container->expects($this->once())->method('get')->with('config')->willReturn($config);

        $twitterMessageBuilder = (new TwitterMessageBuilderFactory())($container);

        $this->assertInstanceOf(TwitterMessageBuilder::class, $twitterMessageBuilder);

    }
}
