<?php

/*
 * This file is part of php-task library.
 *
 * (c) php-task
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Task\TaskBundle\Locking;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Task\Lock\LockStorageInterface;

/**
 * Save locks in Symfony Cache.
 */
class CacheLockStorage implements LockStorageInterface
{

    /**
     * Avoid key collision with a key prefix.
     */
    const KEY_PREFIX = 'php_task_lock__';

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @param CacheInterface $cache
     */
    public function __construct($cache)
    {
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function save($key, $ttl)
    {
        /** @var CacheItemInterface $cacheItem */
        $cacheItem = $this->cache->getItem(self::KEY_PREFIX . $key);
        $cacheItem->set('LOCK');
        $cacheItem->expiresAfter($ttl);
        $this->cache->save($cacheItem);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        try {
            return $this->cache->deleteItem(self::KEY_PREFIX . $key);
        } catch (InvalidArgumentException $e) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function exists($key)
    {
        /** @var CacheItemInterface $cacheItem */
        $cacheItem = $this->cache->getItem(self::KEY_PREFIX . $key);
        return $cacheItem->isHit();
    }

}

