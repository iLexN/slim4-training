<?php
declare(strict_types=1);

namespace App\Services;

use App\ValueObject\Article\Article;

final class ShortUrlService
{
    public function generateArticleUrl(Article $article){
        return $this->gen(10);
    }

    private function gen(int $n):string{
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }
}