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
    private $shortUrlService;

    public function __construct(ShortUrlService $shortUrlService)
    {
        $this->shortUrlService = $shortUrlService;
    }

    public function __invoke(ArticlePreSaveEvent $articlePreSaveEvent): void
    {
        $article = $articlePreSaveEvent->getArticle();
        if (!$article->hasUrl()) {
            $article->url = $this->shortUrlService->generateArticleUrl($article);
        }
    }
}
