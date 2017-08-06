<?php
namespace App\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Zend\Db\Sql\Sql;

class LoadUrlCommand extends CommandAbstract
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:start-process-url-fetch')

            // the short description shown while running "php bin/console list"
            ->setDescription('Start url fetch processing')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('--')
        ;
 
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!extension_loaded('gearman')) {
            throw new \Exception('Gearman must be enabled');
        }
        
        $container = $this->getContainer();
        
        $gearmanClient = $container->get('GearmanClient');
        
        
        $results = $this->fetchSources(array(
            'status_id' => 2,
            'source_type_id' => 1,
        ));
        
        foreach ($results as $result) {
            $gearmanClient->doBackground("addFetchUrls", serialize(['sourceId' => $result['id']]));
        }
        
    }
    
    protected function fetchSources(array $where = array())
    {
        $adapter = $this->getContainer()->get('ArticlesDbAdapter');
        
        $sql = new Sql($adapter);
        
        $select = $sql->select()
                ->from('sources')
                ->where($where);
        
        $statement = $sql->prepareStatementForSqlObject($select);
        
        return $statement->execute();
    }
}