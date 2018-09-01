<?php
declare(strict_types=1);
namespace App\Services\SocialMedia;

use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

/**
 * Class TwitterManager
 * @package App\Services\SocialMedia
 */
class TwitterManager
{
    /**
     * @var Sql
     */
    protected $sql;

    /**
     * TwitterManager constructor.
     * @param Sql $sql
     */
    public function __construct(Sql $sql)
    {
        $this->sql = $sql;
    }

    /**
     * Search method for already posted articles on Twitter
     * @param \DateTime $date
     * @return array
     */
    public function search(\DateTime $date): array
    {
        $select = $this->sql->select()
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
            ->order('articles.id DESC')
            ->limit(100)
        ;

        $statement = $this->sql->prepareStatementForSqlObject($select);

        return $statement->execute()->getResource()->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Find one article that has already been posted
     * @param int $articleId
     * @return bool
     */
    public function find(int $articleId): bool
    {
        $select = $this->sql->select()
            ->columns([
                'count' => new Expression('count(DISTINCT id)')
            ])
            ->from('article_twitter_posts')
            ->where([
                'article_id = ?' => $articleId
            ]);

        $statement = $this->sql->prepareStatementForSqlObject($select);
        return (bool) $statement->execute()->getResource()->fetch(\PDO::FETCH_COLUMN);

    }

    /**
     * Insert record so that this has been posted
     * @param int $articleId
     * @param \DateTime $dateTime
     * @return int
     */
    public function insert(int $articleId, \DateTime $dateTime): int
    {
        $insert = $this->sql->insert()
            ->into('article_twitter_posts')
            ->columns([
                'article_id',
                'posted_datetime',
            ])
            ->values([
                $articleId,
                $dateTime->format('Y-m-d H:i:s')
            ]);

        $statement = $this->sql->prepareStatementForSqlObject($insert);
        $statement->execute();

        return (int) $this->sql->getAdapter()->getDriver()->getConnection()->getLastGeneratedValue();
    }
}
