<?php

declare(strict_types=1);

namespace Domain\Source\Action;

use Domain\Source\DbModel\Source;
use Domain\Source\Model\SourceOperationModel;
use Domain\Source\Model\SourceOperationModelFactory;
use Spatie\QueueableAction\QueueableAction;

final class StatusInActiveSourceAction
{
    use QueueableAction;

    /**
     * @var SourceOperationModelFactory
     */
    private $factory;

    public function __construct(SourceOperationModelFactory $factory)
    {
        $this->factory = $factory;
    }

    public function executeByModel(Source $source): void
    {
        $domain = $this->factory->createOne($source);
        $this->execute($domain);
    }

    public function onQueueByModel(Source $source, $queue = null): void
    {
        $domain = $this->factory->createOne($source);
        $this->onQueue($queue)->execute($domain);
    }

    public function execute(SourceOperationModel $sourceDomain): void
    {
        $sourceDomain->setInactive()->save();
    }
}
