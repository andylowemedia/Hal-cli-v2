<?php
namespace App\Repository;

use Zend\Db\Adapter\Adapter;

abstract class RepositoryAbstract
{
    protected $adapter;
    
    public function __construct(Adapter $adapter)
    {
        $this->setAdapter($adapter);
    }
    
    public function setAdapter(Adapter $adapter)
    {
        $this->adapter = $adapter;
        return $this;
    }
    
    public function getAdapter()
    {
        if (!$this->adapter instanceof Adapter) {
            throw new \InvalidArugmentException('Zend based database adapter must be used for this repository');
        }
        return $this->adapter;
    }

}