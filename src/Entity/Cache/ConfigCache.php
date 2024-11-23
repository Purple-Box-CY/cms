<?php

namespace App\Entity\Cache;

use App\Entity\Config;
use App\Entity\Interfaces\ConfigInterface;

class ConfigCache implements ConfigInterface
{
    public string $key;
    public string $value;

    public static function create(Config $config): self
    {
        $cacheObject = new self();
        $cacheObject->key = $config->getKey();
        $cacheObject->value = $config->getValue();

        return $cacheObject;
    }

    public static function createFromCache(object $stdObject): self
    {
        $cacheObject = new self();
        $cacheObject->key = $stdObject->key;
        $cacheObject->value = $stdObject->value;

        return $cacheObject;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}