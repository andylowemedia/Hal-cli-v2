<?php

namespace AppTest\Services\SocialMedia;


use App\Services\SocialMedia\TwitterMessageBuilder;
use PHPUnit\Framework\TestCase;

class TwitterMessageBuilderTest extends TestCase
{
    public function testTwitterMessageIsBuiltAsExpected()
    {
        $builder = new TwitterMessageBuilder('https://www.yournews365.com/');

        $article = [
            'title' => 'Article test title for use in unit test',
            'slug' => 'article-test-title-for-use-in-unit-test',
        ];

        $expectedMessage = 'Article test title for use in unit test '
            . 'https://www.yournews365.com/article-test-title-for-use-in-unit-test?code=b73c2d22763d1ce2143a3755c1d0ad3a';

        $this->assertEquals($expectedMessage, $builder->build($article));
    }
}
