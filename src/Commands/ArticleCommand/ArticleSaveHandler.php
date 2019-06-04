<?php

declare(strict_types=1);

namespace App\Commands\ArticleCommand;

use App\Event\Article\ArticlePostSaveEvent;
use App\Event\Article\ArticlePreSaveEvent;
use Psr\EventDispatcher\EventDispatcherInterface;

final class ArticleSaveHandler
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function handle(ArticleSaveCommand $articleSaveCommand): void
    {
        $preSaveEvent = new ArticlePreSaveEvent($articleSaveCommand->getArticle());
        $this->eventDispatcher->dispatch($preSaveEvent);

        //save
        dump($preSaveEvent->getArticle());

        $postSaveEvent = new ArticlePostSaveEvent($articleSaveCommand->getArticle());
        $this->eventDispatcher->dispatch($postSaveEvent);
    }
}
