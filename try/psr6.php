<?php

declare(strict_types=1);

use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;

include __DIR__ . '/../vendor/autoload.php';

$client = RedisAdapter::createConnection(
    'redis://localhost'
);

$cache = new RedisAdapter(
    $client,
    $namespace = 'app',
    $defaultLifetime = 5 * 1000 * 30
);

$productsCount = $cache->getItem('person3');
dump(get_class($productsCount));

if (!$productsCount instanceof \Psr\Cache\CacheItemInterface){
    dump('psr CacheItemInterface');
    throw new InvalidArgumentException('s');
}

dump($productsCount->isHit());
dump($productsCount->get());
$cache = new TagAwareAdapter(
    $cache
);

$cache->invalidateTags(['tag_1']);

//$cache->get('person3',function(){
//    return 'a';
//});
//$cache->delete('person3');
