<?php

namespace App\Service\Infrastructure;

use App\Entity\Cache\ConfigCache;
use App\Entity\Config\ConfigTextKeys;
use App\Entity\Interfaces\ConfigInterface;
use App\Repository\ConfigRepository;
use App\Repository\ConfigTextRepository;
use App\Service\Utility\MomentHelper;

class ConfigService
{
    public function __construct(
        private ConfigRepository $configRepository,
        private ConfigTextRepository $configTextRepository,
        private RedisService     $redisService,
    ) {
        $this->redisService->setPrefix(RedisKeys::PREFIX_CONFIG);
    }

    /**
     * @return ConfigInterface[]
     */
    public function getConfigs(): array
    {
        $result = [];
        $configs = $this->redisService->getObjects(RedisKeys::KEY_CONFIG_LIST_ALL, false);
        if ($configs) {
            foreach ($configs as $config) {
                $configDTO = ConfigCache::createFromCache($config);
                $result[$configDTO->getKey()]=$configDTO;
            }

            return $result;
        }

        $configs = $this->configRepository->findAll();

        foreach ($configs as $config) {
            $result[$config->getKey()]=ConfigCache::create($config);
        }

        $this->redisService->setObjects(RedisKeys::KEY_CONFIG_LIST_ALL, $result, MomentHelper::SECONDS_DAY, false);

        return $result;
    }

    /**
     * @return ConfigInterface[]
     */
    public function getPublicConfigs(): array
    {
        $result = [];
        $configs = $this->redisService->getObjects(RedisKeys::KEY_CONFIG_LIST_PUBLIC, false);
        if ($configs) {
            foreach ($configs as $config) {
                $result[]=ConfigCache::createFromCache($config);
            }

            return $result;
        }

        $configs = $this->configRepository->findBy([
            'public' => true,
        ]);

        foreach ($configs as $config) {
            $result[]=ConfigCache::create($config);
        }

        $this->redisService->setObjects(RedisKeys::KEY_CONFIG_LIST_PUBLIC, $result, MomentHelper::SECONDS_DAY, false);

        return $result;
    }

    public function getBoolConfig(string $key): bool
    {
        $configs = $this->getConfigs();
        if (!isset($configs[$key])) {
            return false;
        }

        return in_array($configs[$key]->getValue(), ['true', '1', 1]);
    }

    public function getIntConfig(string $key): ?int
    {
        $configs = $this->getConfigs();
        if (!isset($configs[$key])) {
            return null;
        }

        return (int)($configs[$key]->getValue());
    }


    public function getConfigText(string $key): string
    {
        $configText = $this->configTextRepository->findOneBy([
            'key' => $key,
        ]);

        if ($configText && $configText->getValue()) {
            return $configText->getValue();
        }

        return ConfigTextKeys::DEFAULT_VALUES[$key] ?? '';
    }
}
