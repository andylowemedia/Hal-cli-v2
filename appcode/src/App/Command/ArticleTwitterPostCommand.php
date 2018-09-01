<?php
namespace App\Command;

use App\Services\SocialMedia\TwitterManager;
use App\Services\SocialMedia\TwitterMessageBuilder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ZendService\Twitter\Twitter;

class ArticleTwitterPostCommand extends Command
{
    protected $baseUrl = "https://www.yournews365.com/";
    private $twitterManager;
    private $zendServiceTwitter;
    private $builder;

    public function __construct(
        TwitterManager $twitterManager,
        Twitter $zendServiceTwitter,
        TwitterMessageBuilder $builder
    )
    {
        $this->twitterManager = $twitterManager;
        $this->zendServiceTwitter = $zendServiceTwitter;
        $this->builder = $builder;
        parent::__construct();
    }
    
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:post-article-twitter')

            // the short description shown while running "php bin/console list"
            ->setDescription('Post Article on Twitter')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('--')
        ;
 
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dateTime = new \DateTime;

        $data = $this->twitterManager->search($dateTime);

        foreach ($data as $row) {
            $postExists = $this->twitterManager->find($row['id']);
            if ($postExists) {
                continue;
            }
            
            try {
                $tweet = $this->builder($row);
                $this->zendServiceTwitter->statuses->update($tweet);
                echo $this->twitterManager->insert($row['id'], $dateTime) . "\n";
                sleep(60);
            } catch (\Exception $e) {
                echo $e->getMessage() . "\n";
                echo $e->getTraceAsString() . "\n";
                echo "****************************************************\n";
            }
        }
        
        return $this;
    }
}
