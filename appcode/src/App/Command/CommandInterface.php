<?php
namespace App\Command;

interface CommandInterface
{
    public function setContainer($container);
    
    public function getContainer();
}