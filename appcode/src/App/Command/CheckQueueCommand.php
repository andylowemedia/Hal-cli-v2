<?php
namespace App\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Aws\Ec2\Ec2Client;

use App\Repository\Queue;

class CheckQueueCommand extends CommandAbstract
{
    
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:check-queue')

            // the short description shown while running "php bin/console list"
            ->setDescription('Check queue')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('--')
        ;
 
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $verbose = $input->getOption('verbose');
        
        $scrapeInstance = $this->getContainer()->get(\App\Server\ScrapeInstances::class);
        
        $scrapeInstance->process();
        
        
        return $this;
    }
}