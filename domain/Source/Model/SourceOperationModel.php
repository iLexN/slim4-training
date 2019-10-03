<?php

declare(strict_types=1);


namespace Domain\Source\Model;

use Domain\Source\DbModel\Source;
use Domain\Support\Enum\Status;
use Illuminate\Support\Carbon;

final class SourceOperationModel
{
    /**
     * @var Source
     */
    private $source;

    public function __construct(Source $source)
    {
        $this->source = $source;
    }

    public function save(): void
    {
        $this->source->save();
    }

    public function setActive(): SourceOperationModel
    {
        $this->source->status = Status::active()->getValue();
        return $this;
    }

    public function setInactive(): SourceOperationModel
    {
        $this->source->status = Status::inActive()->getValue();
        return $this;
    }

    public function setLastSync(): SourceOperationModel
    {
        $this->source->last_sync = Carbon::now();
        return $this;
    }
}
