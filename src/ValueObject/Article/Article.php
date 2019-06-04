<?php

declare(strict_types=1);

namespace App\ValueObject\Article;

final class Article
{

    /**
     * @var int
     */
    private const SUMMARY_LENGTH = 2;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $description;

    /**
     * @var int
     */
    public $status;

    /**
     * @var string
     */
    public $url = '';

    /**
     * @var string
     */
    public $summary = '';

    public function __construct(
        string $title,
        string $description,
        int $status,
        string $summary = '',
        string $url = ''
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->status = $status;
        $this->url = $url;
        $this->summary = $summary;
    }

    public function hasSummary(): bool
    {
        return '' !== $this->summary;
    }

    public function descriptionToSummary(): string
    {
        return substr($this->description, 0, self::SUMMARY_LENGTH);
    }

    public function hasUrl(): bool
    {
        return '' !== $this->url;
    }
}
