<?php

declare(strict_types=1);

namespace App\Services;

use App\ValueObject\Article\Article;
use function strlen;

final class ShortUrlService
{
    public function generateArticleUrl(Article $article): string
    {
        return $article->title . $this->gen(10);
    }

    private function gen(int $num): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $num; $i++) {
            $index = \random_int(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }
}
