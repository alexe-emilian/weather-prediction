<?php

namespace App\Tests\Service;

use App\Service\PartnerDataCache;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class PartnerDataCacheTest extends TestCase
{
    private FilesystemAdapter $cache;

    private PartnerDataCache $partnerDataCache;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cache = $this->createMock(FilesystemAdapter::class);
        $this->partnerDataCache = new PartnerDataCache($this->cache);
    }

    public function testCacheIsHit(): void
    {
        $cacheItem = $this->createMock(CacheItemInterface::class);
        $cacheItem->expects($this->once())->method('isHit')->willReturn(true);
        $cacheItem->expects($this->once())->method('get')->willReturn([]);
        $this->cache->expects($this->once())->method('getItem')->willReturn($cacheItem);

        $result = $this->partnerDataCache->getData('testKey');

        $this->assertEquals([], $result);
    }

    public function testCacheIsNotHit(): void
    {
        $cacheItem = $this->createMock(CacheItemInterface::class);
        $cacheItem->expects($this->once())->method('isHit')->willReturn(false);
        $this->cache->expects($this->once())->method('getItem')->willReturn($cacheItem);

        $result = $this->partnerDataCache->getData('testKey');

        $this->assertEquals(null, $result);
    }

    public function testDataIsSet(): void
    {
        $dataToCache = ['data'];

        $cacheItem = $this->createMock(CacheItemInterface::class);
        $cacheItem->expects($this->once())->method('set')->with($dataToCache);
        $cacheItem->expects($this->once())->method('expiresAfter')->with(60);
        $this->cache->expects($this->once())->method('getItem')->willReturn($cacheItem);

        $this->cache->expects($this->once())->method('save')->with($cacheItem);

        $this->partnerDataCache->setData('testKey', $dataToCache);
    }
}
