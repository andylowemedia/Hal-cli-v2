<?php

namespace AppTest\Services\SocialMedia;

use App\Services\SocialMedia\TwitterManager;
use PHPUnit\Framework\TestCase;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\Driver\ConnectionInterface;
use Zend\Db\Adapter\Driver\DriverInterface;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Adapter\Driver\StatementInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

class TwitterManagerTest extends TestCase
{
    private $adapter;

    public function setUp()/* The :void return type declaration that should be here would cause a BC issue */
    {
        parent::setUp();

    }

    public function testSelectQueryBuildsAsExpected()
    {
        $adapter = $this->getMockBuilder(AdapterInterface::class)->getMockForAbstractClass();

        $resource = $this->getMockBuilder(\PDOStatement::class)->setMethods(['fetchAll'])->getMock();
        $resource->expects($this->once())
            ->method('fetchAll')
            ->willReturn([]);


        $result = $this->getMockBuilder(ResultInterface::class)
            ->setMethods(['getResource'])
            ->getMockForAbstractClass();

        $result->expects($this->once())
            ->method('getResource')
            ->willReturn($resource);


        $statement = $this->getMockBuilder(StatementInterface::class)
            ->setMethods(['execute'])
            ->getMockForAbstractClass();

        $statement->expects($this->once())
            ->method('execute')
            ->willReturn($result);

        $sql = $this->getMockBuilder(Sql::class)
            ->setConstructorArgs([$adapter])
            ->setMethods(['prepareStatementForSqlObject'])
            ->getMock();

        $date = new \DateTime();

        $sql->expects($this->once())
            ->method('prepareStatementForSqlObject')
            ->with($this->buildSelectQueryObject($adapter, $date))
            ->willReturn($statement);

        $manager = new TwitterManager($sql);
        $manager->search($date);
    }

    private function buildSelectQueryObject($adapter, $date)
    {
        $sql = new Sql($adapter);
        $select = $sql->select()
            ->columns([
                'id',
                'title',
                'slug',
            ])
            ->from('articles')
            ->join('article_twitter_posts', 'articles.id = article_twitter_posts.article_id', array(), Select::JOIN_LEFT)
            ->where([
                'article_twitter_posts.id is null',
                'articles.status_id = 2',
            ])
            ->order('articles.id DESC');

        return $select;
    }

    public function testInsertQueryBuildsAsExpected()
    {
        $connection = $this->getMockBuilder(ConnectionInterface::class)
            ->setMethods(['getLastGeneratedValue'])
            ->getMockForAbstractClass();

        $connection->expects($this->once())
            ->method('getLastGeneratedValue')
            ->willReturn(1);

        $driver = $this->getMockBuilder(DriverInterface::class)
            ->setMethods(['getConnection'])
            ->getMockForAbstractClass();

        $driver->expects($this->once())
            ->method('getConnection')
            ->willReturn($connection);


        $adapter = $this->getMockBuilder(AdapterInterface::class)
            ->setMethods(['getDriver'])
            ->getMockForAbstractClass();

        $adapter->expects($this->once())
            ->method('getDriver')
            ->willReturn($driver);

        $date = new \DateTime();

        $statement = $this->getMockBuilder(StatementInterface::class)
            ->setMethods(['execute'])
            ->getMockForAbstractClass();

        $statement->expects($this->once())
            ->method('execute');


        $sql = $this->getMockBuilder(Sql::class)
            ->setConstructorArgs([$adapter])
            ->setMethods(['prepareStatementForSqlObject'])
            ->getMock();

        $sql->expects($this->once())
            ->method('prepareStatementForSqlObject')
            ->with($this->buildInsertQueryObject($adapter,1, $date))
            ->willReturn($statement);

        $manager = new TwitterManager($sql);
        $manager->insert(1, $date);

    }

    private function buildInsertQueryObject($adapter, $articleId, $dateTime)
    {
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

        return $insert;
    }

    public function testFindQueryBuildsAsExpected()
    {
        $adapter = $this->getMockBuilder(AdapterInterface::class)->getMockForAbstractClass();

        $resource = $this->getMockBuilder(\PDOStatement::class)->setMethods(['fetch'])->getMock();
        $resource->expects($this->once())
            ->method('fetch');


        $result = $this->getMockBuilder(ResultInterface::class)
            ->setMethods(['getResource'])
            ->getMockForAbstractClass();

        $result->expects($this->once())
            ->method('getResource')
            ->willReturn($resource);


        $statement = $this->getMockBuilder(StatementInterface::class)
            ->setMethods(['execute'])
            ->getMockForAbstractClass();

        $statement->expects($this->once())
            ->method('execute')
            ->willReturn($result);

        $sql = $this->getMockBuilder(Sql::class)
            ->setConstructorArgs([$adapter])
            ->setMethods(['prepareStatementForSqlObject'])
            ->getMock();

        $date = new \DateTime();

        $sql->expects($this->once())
            ->method('prepareStatementForSqlObject')
            ->with($this->buildFindQueryObject($adapter, 1))
            ->willReturn($statement);

        $manager = new TwitterManager($sql);
        $manager->find(1);

    }

    private function buildFindQueryObject(AdapterInterface $adapter, int $articleId)
    {
        $sql = new Sql($adapter);

        $select = $sql->select()
            ->columns([
                'count' => new Expression('count(DISTINCT id)')
            ])
            ->from('article_twitter_posts')
            ->where([
                'article_id = ?' => $articleId
            ]);
        return $select;
    }
}
