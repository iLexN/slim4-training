<?php

declare(strict_types=1);

namespace App\EventListener\Article\PreSave;

use App\Event\Article\ArticlePreSaveEvent;

final class ArticleDescriptionToSummaryListener
{
    public function __invoke(ArticlePreSaveEvent $articlePreSaveEvent): void
    {
        $article = $articlePreSaveEvent->getArticle();
        dump('aa');
        if (!$article->hasSummary()) {
            dump('bb');
            $article->summary = $article->descriptionToSummary();
        }
    }
}
