<?php

namespace App\Service;

use Psr\Cache\CacheItemInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class PartnerDataCache
{
    private FilesystemAdapter $cache;

    public function __construct(FilesystemAdapter $cache)
    {
        $this->cache = $cache;
    }

    public function getData(string $cacheKey): ?array
    {
        /** @var CacheItemInterface $cachedPredictions */
        $cachedPredictions = $this->cache->getItem($cacheKey);
        if ($cachedPredictions->isHit()) {
            return $cachedPredictions->get();
        }

        return null;
    }

    public function setData(string $cacheKey, array $data): void
    {
        /** @var CacheItemInterface $cachedPredictions */
        $cachedPredictions = $this->cache->getItem($cacheKey);
        $cachedPredictions->set($data);
        $cachedPredictions->expiresAfter(60);

        $this->cache->save($cachedPredictions);
    }
}
