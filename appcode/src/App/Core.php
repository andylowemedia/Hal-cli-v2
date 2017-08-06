<?php
namespace App;

use Symfony\Component\Console\Application;

class Core
{
    private $container;
    private $application;
    
    public function __construct($container)
    {
        $this->setContainer($container);
        $this->init();
    }
    
    public function init()
    {
        $this->setApplication(new Application);
    }
    
    public function setContainer($container)
    {
        $this->container = $container;
        return $this;
    }
    
    public function getContainer()
    {
        return $this->container;
    }
    
    public function setApplication($application)
    {
        $this->application = $application;
        return $this;
    }
    
    public function getApplication()
    {
        return $this->application;
    }
    
    public function run()
    {
        $application = $this->getApplication();

        $this->addCommands();

        $application->run();
    }
    
    protected function addCommands()
    {
        $application = $this->getApplication();
        
        $container = $this->getContainer();
        
        if (!$container->has('config')) {
            throw new \RuntimeException('Configuration must be added into the service container for use in the application');
        }
        
        $config = $container->get('config');
        
        foreach ($config['commands'] as $command) {
            $application->add(new $command($container));
        }
        
        return $this;
    }
}