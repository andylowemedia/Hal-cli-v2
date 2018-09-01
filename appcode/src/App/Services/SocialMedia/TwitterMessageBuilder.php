<?php
declare(strict_types=1);

namespace App\Services\SocialMedia;

/**
 * Class TwitterMessageBuilder
 * @package App\Services\SocialMedia
 */
class TwitterMessageBuilder
{
    /**
     * @var string
     */
    private $baseUrl;

    /**
     * TwitterMessageBuilder constructor.
     * @param string $baseUrl
     */
    public function __construct(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * Builds message ready for posting on to Twitter
     * @param array $article
     * @return string
     */
    public function build(array $article): string
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
}
