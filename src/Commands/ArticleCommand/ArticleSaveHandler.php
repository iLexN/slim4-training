<?php
declare(strict_types=1);

namespace App\Commands\ArticleCommand;

use App\Event\Article\ArticlePostSaveEvent;
use App\Event\Article\ArticlePreSaveEvent;
use Psr\EventDispatcher\EventDispatcherInterface;

final class ArticleSaveHandler
{

    /**
     * @var \Psr\EventDispatcher\EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function handle(ArticleSaveCommand $articleSaveCommand):void
    {
        $preSaveEvent = new ArticlePreSaveEvent($articleSaveCommand->getArticle());
        $this->dispatcher->dispatch($preSaveEvent);

        //save
        dump($preSaveEvent->getArticle());

        $postSaveEvent = new ArticlePostSaveEvent($articleSaveCommand->getArticle());
        $this->dispatcher->dispatch($postSaveEvent);
    }
}