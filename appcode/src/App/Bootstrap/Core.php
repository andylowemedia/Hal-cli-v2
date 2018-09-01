<?php
declare(strict_types=1);
namespace App\Bootstrap;

use Interop\Container\ContainerInterface;
use Symfony\Component\Console\Application;

/**
 * Class Core
 * @package App
 */
class Core
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var Application
     */
    private $application;

    /**
     * Core constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
        $this->init();
    }

    /**
     * Init method to prep symfony application
     * @return Core
     */
    public function init(): self
    {
        $this->setApplication(new Application);
        return $this;
    }

    /**
     * Sets container
     * @param ContainerInterface $container
     * @return Core
     */
    private function setContainer(ContainerInterface $container): self
    {
        $this->container = $container;
        return $this;
    }

    /**
     * Gets container
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * Sets Symfony console application
     * @param Application $application
     * @return Core
     */
    public function setApplication(Application $application): self
    {
        $this->application = $application;
        return $this;
    }
    
    public function getApplication(): Application
    {
        return $this->application;
    }

    /**
     * Runs application after adding commands registered in the service container and config
     * @return Core
     * @throws \Exception
     */
    public function run(): self
    {
        $application = $this->getApplication();

        $this->addCommands();

        $application->run();

        return $this;
    }

    /**
     * Adds Symfony console commands
     * @return Core
     */
    protected function addCommands(): self
    {
        $application = $this->getApplication();
        
        $container = $this->getContainer();

        if (!$container->has('config')) {
            throw new \RuntimeException('Configuration must be added into the service container for use in the application');
        }

        $config = $container->get('config');

        foreach ($config['commands'] as $command) {
            if ($container->has($command)) {
                $application->add($container->get($command));
            }
        }
        
        return $this;
    }
}