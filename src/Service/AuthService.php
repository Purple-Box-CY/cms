<?php

namespace App\Service;

use App\Entity\User;
use App\Service\Infrastructure\LogService;
use App\Service\Utility\DomainHelper;
use GuzzleHttp\Client;

class AuthService
{
    private const ROUTE_GET_AUTH_HASH = '/api/auth/cms';
    public const ROUTE_FRONT_AUTH_HASH = '/user/auth?hash=%s&auth_type=%s';

    public const AUTH_TYPE_CMS = 'cms';
    public const AUTH_TYPE_GOOGLE = 'GOOGLE';
    public const AUTH_TYPE_FACEBOOK = 'FACEBOOK';
    public const AUTH_TYPE_INSTAGRAM = 'instagram';
    public const AUTH_TYPE_ANONYM = 'anonym';
    public const AVAILABLE_AUTH_TYPES = [
        self::AUTH_TYPE_CMS,
        self::AUTH_TYPE_GOOGLE,
        self::AUTH_TYPE_FACEBOOK,
        self::AUTH_TYPE_INSTAGRAM,
        self::AUTH_TYPE_ANONYM
    ];

    private const AI_SERVER_TIMEOUT = 10;

    private Client $client;

    public function __construct(
        private readonly string     $authPass,
        private readonly string     $appEnv,
        private LogService $logger,
    ) {
        $this->client = new Client(['base_uri' => DomainHelper::getApiProjectDomainForCommand($appEnv, false)]);
    }

    public function getAuthHashByUser(User $user): ?string
    {
        $params = [
            'uid'  => $user->getUidStr(),
            'sign' => $this->authPass,
        ];

        try {
            $result = $this->client->get(self::ROUTE_GET_AUTH_HASH,
                [
                    'query'            => $params,
                    'transfer_timeout' => self::AI_SERVER_TIMEOUT,
                    'timeout'          => self::AI_SERVER_TIMEOUT,
                    'connect_timeout'  => self::AI_SERVER_TIMEOUT,
                ]);

            if ($result->getStatusCode() != 200) {
                $this->logger->error('Failed to get user hash',
                    [
                        'user_uid'    => $user->getUidStr(),
                        'status_code' => $result->getStatusCode(),
                    ]);

                return null;
            }
            $response = json_decode($result->getBody(), true);

            return $response['hash'] ?? '';
        } catch (\Exception $e) {
            $this->logger->warning('Failed to get user hash',
                [
                    'error'    => $e->getMessage(),
                    'user_uid' => $user->getUidStr(),
                ]);

            return null;
        }
    }
}
