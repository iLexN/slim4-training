<?php

declare(strict_types=1);

namespace App\Commands\ArticleCommand;

use App\ValueObject\Article\Article;

final class ArticleGenerateUrlCommand
{
    /**
     * @var Article
     */
    private $article;

    public function __construct(Article $article)
    {
        $this->article = $article;
    }

    /**
     * @return Article
     */
    public function getArticle(): Article
    {
        return $this->article;
    }
}
