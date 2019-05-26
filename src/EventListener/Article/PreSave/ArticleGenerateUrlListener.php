<?php

declare(strict_types=1);

namespace App\EventListener\Article\PreSave;

use App\Event\Article\ArticlePreSaveEvent;
use App\Services\ShortUrlService;

final class ArticleGenerateUrlListener
{
    /**
     * @var ShortUrlService
     */
    private $service;

    public function __construct(ShortUrlService $service)
    {
        $this->service = $service;
    }

    public function __invoke(ArticlePreSaveEvent $event): void
    {
        $article = $event->getArticle();
        if (!$article->hasUrl()) {
            $article->url = $this->service->generateArticleUrl($article);
        }
    }
}
