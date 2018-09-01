<?php

namespace AppTest\Bootstrap;

use App\Bootstrap\Core;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

class CoreTest extends TestCase
{
    private $container;

    public function setUp()
    {
        parent::setUp();

        $this->container = $this->getMockBuilder(ContainerInterface::class)
            ->setMethods(['get', 'has'])
            ->getMockForAbstractClass();
    }

    public function testSetAndGetContainerMethodsWork()
    {
        $core = new Core($this->container);

        $this->assertEquals($this->container, $core->getContainer());
    }

    public function testGetApplicationReturnsSymfonyApplicationAfterBeingSetInInitMethod()
    {
        $core = new Core($this->container);

        $this->assertInstanceOf(Application::class, $core->getApplication());
    }

    public function testSetAndGetApplicationMethodsWork()
    {
        $core = new Core($this->container);

        $application = new Application();

        $this->assertEquals($application, $core->getApplication());
    }

    public function testRunExecutesRunMethodAfterHavingOneCommandAdded()
    {
        $core = new Core($this->container);

        $command = $this->getMockBuilder(Command::class)->disableOriginalConstructor()->getMock();

        $this->container->expects($this->exactly(2))
            ->method('has')
            ->withConsecutive([$this->equalTo('config')], [$this->equalTo('test-command')])
            ->willReturn(true);

        $this->container->expects($this->exactly(2))
            ->method('get')->withConsecutive($this->equalTo('config'))
            ->willReturnOnConsecutiveCalls(['commands' => ['test-command']], $command);

        $application = $this->getMockBuilder(Application::class)->setMethods(['run', 'add'])->getMock();

        $application->expects($this->once())->method('run');
        $application->expects($this->once())->method('add')->with($command);

        $core->setApplication($application);

        $core->run();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testRunExecutesRunMethodAndExceptionthrownIfConfigNotSetInServiceContainer()
    {
        $core = new Core($this->container);

        $this->container->expects($this->exactly(1))
            ->method('has')
            ->with($this->equalTo('config'))
            ->willReturn(false);

        $application = $this->getMockBuilder(Application::class)->setMethods(['run', 'add'])->getMock();

        $core->setApplication($application);

        $core->run();
    }
}
