<?php
namespace App\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ZendService\Twitter\Twitter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;

class ArticleTwitterPostCommand extends CommandAbstract
{
    protected $baseUrl = "https://www.yournews365.com/";
    
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
            $postExists = $this->checkTwitterPost($row['id']);
            if ($postExists) {
                continue;
            }
            
            try {
                $dateTime = new \DateTime;
                
                $tweet = $this->buildTwitterPost($row);
                $twitter->statuses->update($tweet);
                echo $this->saveTwitterPost($row['id'], $dateTime) . "\n";
                sleep(60);
            } catch (\Exception $e) {
                echo $e->getMessage() . "\n";
                echo $e->getTraceAsString() . "\n";
                echo "****************************************************\n";
            }
        }
        
        return $this;
    }
    
    protected function saveTwitterPost($articleId, \DateTime $dateTime)
    {
        $container = $this->getContainer();
        
        $adapter = $container->get('ArticlesDbAdapter');
        
        $sql = new Sql($adapter);
        
        $insert = $sql->insert()
                ->into('article_twitter_posts')
                ->columns([
                    'article_id',
                    'posted_datetime',
                ])
                ->values([
                    $articleId,
                    $dateTime->format('Y-m-d H:i:s')
                ]);
        
        $statement = $sql->prepareStatementForSqlObject($insert);
        $statement->execute();
        
        return $adapter->getDriver()->getConnection()->getLastGeneratedValue();
    }
    
    protected function buildTwitterPost(array $article)
    {
        $url = $this->baseUrl . $article['slug'] . "?code=b73c2d22763d1ce2143a3755c1d0ad3a";

        $text = " " . $url;


        $title = htmlentities($article['title']);
        $title = str_replace('&ndash;', '', $title);
        $title = str_replace('&lsquo;', '', $title);
        $title = str_replace('&rsquo;', '', $title);
        $title = str_replace('&amp;nbsp;', ' ', $title);
        $title = str_replace('&amp;amp;', ' ', $title);
        $title = str_replace('&eacute;', ' ', $title);

        $tweet = substr(strip_tags($title), 0, (240 - strlen($text))) . $text;
        
        return $tweet;
    }
    
    protected function checkTwitterPost($articleId)
    {
        $container = $this->getContainer();
        
        $adapter = $container->get('ArticlesDbAdapter');
        
        $sql = new Sql($adapter);
        
        $select = $sql->select()
                ->columns([
                    'count' => new Expression('count(DISTINCT id)')
                ])
                ->from('article_twitter_posts')
                ->where([
                    'article_id = ?' => $articleId
                ]);
        
        $statement = $sql->prepareStatementForSqlObject($select);
        return (bool) $statement->execute()->getResource()->fetch(\PDO::FETCH_COLUMN);

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