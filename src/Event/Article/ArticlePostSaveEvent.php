<?php
declare(strict_types=1);

namespace App\Event\Article;

use App\ValueObject\Article\Article;

final class ArticlePostSaveEvent
{

    /**
     * @var \App\ValueObject\Article\Article
     */
    private $article;

    public function __construct(Article $article)
    {
        $this->article = $article;
    }

    /**
     * @return \App\ValueObject\Article\Article
     */
    public function getArticle(): Article
    {
        return $this->article;
    }
}