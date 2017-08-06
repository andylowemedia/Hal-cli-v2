<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;

class CommandAbstract extends Command implements CommandInterface
{
    private $container;
    
    public function __construct($container, $name = null)
    {
        $this->setContainer($container);
        parent::__construct($name);
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
}