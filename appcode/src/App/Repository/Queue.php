<?php
namespace App\Repository;

use Zend\Db\Sql\Sql, Zend\Db\Sql\Expression;

class Queue extends RepositoryAbstract
{
    public function count()
    {
        $sql = new Sql($this->getAdapter());
        $select = $sql->select()
                ->columns(array(
                    'count' => new Expression('COUNT(*)')
                ))
                ->from('gearman_queue');
        
        $statement = $sql->prepareStatementForSqlObject($select);
        
        $result = $statement->execute()->current();
        
        return $result['count'];
    }
}