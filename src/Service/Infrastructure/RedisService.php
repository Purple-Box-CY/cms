<?php

namespace App\Service\Infrastructure;

use App\Entity\Article;
use App\Entity\Marker;
use App\Entity\User;
use App\Service\Exception\RedisQueueNotFoundException;
use App\Service\Utility\MomentHelper;
use Symfony\Component\Serializer\SerializerInterface;
use SymfonyBundles\RedisBundle\Redis\ClientInterface;

class RedisService
{
    public function __construct(
        private bool                $redisIsEnable,
        private bool                $redisQueueIsEnable,
        private ClientInterface     $redis,
        private SerializerInterface $serializer,
        private LogService          $logger,
        private string              $prefix = RedisKeys::PREFIX_MAIN,
    ) {
    }

    public function isEnable(): bool
    {
        return $this->redisIsEnable;
    }

    public function isQueueEnable(): bool
    {
        return $this->redisQueueIsEnable;
    }

    public function setPrefix(string $prefix): void
    {
        $this->prefix = $prefix;
    }

    public function find(string $key, bool $withPrefix = true): ?string
    {
        if (!$this->isEnable()) {
            return null;
        }

        $redisKey = $withPrefix ? $this->getRedisKey($key) : $key;

        return $this->redis->get($redisKey);
    }

    public function get(string $key, bool $withPrefix = true): ?string
    {
        return $this->find($key, $withPrefix);
    }

    public function findArrayValue(string $key): ?array
    {
        if (!$this->isEnable()) {
            return null;
        }

        return $this->redis->hgetall($this->getRedisKey($key));
    }

    public function set(
        string $key,
        string $value,
        int    $ttl = MomentHelper::SECONDS_MINUTE,
        bool   $withPrefix = true
    ): void {
        if (!$this->isEnable()) {
            return;
        }

        if ($withPrefix) {
            $key = $this->getRedisKey($key);
        }

        $this->redis->set($key, $value);
        $this->redis->expire($key, $ttl);
    }

    public function setArrayObjects(string $key, array $value, int $ttl = MomentHelper::SECONDS_MINUTE): void
    {
        if (!$this->isEnable()) {
            return;
        }

        $serializeValue = json_encode($this->serializer->serialize($value, 'json'));
        $key = $this->getRedisKey($key);

        $this->redis->set($key, $serializeValue);
        $this->redis->expire($key, $ttl);
    }

    public function getArrayObjects(string $key): ?array
    {
        if (!$this->isEnable()) {
            return null;
        }

        $res = $this->redis->get($this->getRedisKey($key));
        if ($res === null) {
            return null;
        }

        return json_decode($res, true);
    }


    public function getArray(string $key): ?array
    {
        if (!$this->isEnable()) {
            return null;
        }

        $res = $this->redis->get($key);
        if ($res === null) {
            return null;
        }

        return unserialize($res);
    }

    public function setArray(string $key, array $value, int $ttl = MomentHelper::SECONDS_MINUTE): void
    {
        if (!$this->isEnable()) {
            return;
        }

        $serializeValue = serialize($value);

        $this->redis->set($key, $serializeValue);
        $this->redis->expire($key, $ttl);
    }

    public function pushToStack(string $stackName, mixed $payload): int
    {
        if (!is_array($payload)) {
            $payload = [$payload];
        }

        return $this->redis->rpush($stackName, $payload);
    }

    public function getStack(string $stackName): array
    {
        return $this->redis->lrange($stackName, 0, -1);
    }

    public function setObjects(string $key, array $object, int $ttl = MomentHelper::SECONDS_MINUTE): void
    {
        if (!$this->isEnable()) {
            return;
        }

        $key = $this->getRedisKey($key);

        $json = json_encode($object);
        $value = igbinary_serialize($json);

        $this->redis->set($key, $value);
        $this->redis->expire($key, $ttl);
    }

    public function getObjects(string $key): ?array
    {
        try {
            $findResult = $this->find($key);
            if ($findResult === null) {
                return null;
            }

            $json = igbinary_unserialize($findResult);

            $arrList = json_decode($json);

            return is_array($arrList) ? $arrList : null;
        } catch (\Exception $e) {
            $this->logger->error('Failed to get objects from redis cache',
                [
                    'key'   => $key,
                    'error' => $e->getMessage(),
                ]);

            return null;
        }
    }

    public function removeByPattern(string $pattern, bool $withPrefix = true): void
    {
        if (!$this->isEnable()) {
            return;
        }

        $redisPattern = $withPrefix ? $this->getRedisKey($pattern) : $pattern;
        $keys = $this->getKeys($redisPattern);
        foreach ($keys as $key) {
            $this->remove($key, false);
        }
    }

    public function remove(string $key, bool $withPrefix = true): void
    {
        if (!$this->isEnable()) {
            return;
        }

        $key = $withPrefix ? $this->getRedisKey($key) : $key;
        $this->redis->remove($key);
    }

    public function has(string $key): ?bool
    {
        if (!$this->isEnable()) {
            return null;
        }

        return (bool)$this->find($this->getRedisKey($key));
    }

    private function getRedisKey(string $key): string
    {
        return sprintf('%s:%s', $this->prefix, $key);
    }

    /**
     * @throws RedisQueueNotFoundException
     */
    private function checkQueue(string $queueName): void
    {
        if (!in_array($queueName, RedisKeys::AVAILABLE_QUEUES)) {
            throw new RedisQueueNotFoundException($queueName);
        }
    }

    /**
     * @throws RedisQueueNotFoundException
     */
    public function pushToQueue(string $queueName, mixed $payload): int
    {
        $this->checkQueue($queueName);

        if (!is_array($payload)) {
            $payload = [$payload];
        }

        return $this->redis->rpush($queueName, $payload);
    }

    /**
     * @throws RedisQueueNotFoundException
     */
    public function popFromQueue(string $queueName): mixed
    {
        $this->checkQueue($queueName);

        return $this->redis->lpop($queueName);
    }

    public function getKeys(string $pattern = '*'): ?array
    {
        if (!$this->isEnable()) {
            return null;
        }

        return $this->redis->keys($pattern);
    }

    public function invalidateUserCache(User $user): void
    {
        $redisKeys = [];

        $prefix = sprintf('%s:%s', RedisKeys::PREFIX_USER, RedisKeys::KEY_USER_INFO);
        $redisKeys[] = sprintf($prefix, $user->getUid());

        $prefix = sprintf('%s:%s', RedisKeys::PREFIX_USER, RedisKeys::KEY_USER_ITEM);
        $redisKeys[] = sprintf($prefix, $user->getUid());
        $redisKeys[] = sprintf($prefix, $user->getId());
        $redisKeys[] = sprintf($prefix, $user->getUsername());

        $redisKeys[] = sprintf('%s:%s', RedisKeys::PREFIX_JWT, $user->getEmail());

        $this->clear($redisKeys);
    }


    public function invalidateCacheArticle(Article $article): void
    {
        $this->clear([
            sprintf(RedisKeys::KEY_ARTICLE_ITEM, $article->getAlias()),
        ]);
    }

    public function invalidateCacheMarker(Marker $marker): void
    {
        $this->clear([
            sprintf(RedisKeys::KEY_MARKERS, '*'),
        ]);
    }

    public function clear(array $redisKeys): void
    {
        foreach ($redisKeys as $redisKey) {
            $this->removeByPattern(
                pattern: $redisKey,
                withPrefix: false,
            );
        }
    }
}
