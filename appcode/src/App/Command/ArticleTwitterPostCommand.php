<?php
namespace App\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ZendService\Twitter\Twitter;
use Zend\Db\Sql\Sql, Zend\Db\Sql\Select;

class ArticleTwitterPostCommand extends CommandAbstract
{
    
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
        $twitter = $this->setupTwitter();
        
        $data = $this->fetchData();
        
        foreach ($data as $row) {
            print_r($row);
        }
        
        return $this;
    }
    
    protected function setupTwitter()
    {
        $container = $this->getContainer();
        
        $configApp = $container->get('config');
        
        $config = array(
            'access_token' => array(
                'token'  => $configApp['twitter']['yournews365']['token'],
                'secret' => $configApp['twitter']['yournews365']['secret'],
            ),
            'oauth_options' => array(
                'consumerKey' => $configApp['twitter']['yournews365']['apiKey'],
                'consumerSecret' => $configApp['twitter']['yournews365']['apiSecret'],
            ),
            'http_client_options' => array(
                'adapter' => 'Zend\Http\Client\Adapter\Curl',
                'curloptions' => array(
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_SSL_VERIFYPEER => false,
                ),
            ),
        );

        $twitter = new Twitter($config);
        return $twitter;
    }
    
    
    
    protected function fetchData()
    {
        $container = $this->getContainer();
        
        $adapter = $container->get('ArticlesDbAdapter');
        
        $date = new \DateTime('2018-02-01');
        
        $sql = new Sql($adapter);
        
        $select = $sql->select()
                ->columns([
                    'id',
                    'title',
                    'slug',
                ])
                ->from('articles')
                ->join('article_twitter_posts', 'articles.id = article_twitter_posts.article_id', array(), Select::JOIN_LEFT)
                ->join('featured_articles', 'articles.id = featured_articles.article_id', array(), Select::JOIN_INNER)
                ->where([
                    'article_twitter_posts.id is null',
                    'articles.status_id = 2',
                    'articles.date like ?' => $date->format('Y-m-d') . "%"
                ]);
        $statement = $sql->prepareStatementForSqlObject($select);
        
        return $statement->execute()->getResource()->fetchAll(\PDO::FETCH_ASSOC);
    }
    
}