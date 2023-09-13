<?php

namespace Tb07\DouKuaiOpenApi\JinRiTeMai;

use Tb07\DouKuaiOpenApi\Kernel\Core\ServiceContainer;

/**
 * 抖音的开发接口
 * Class Application
 * @package Tb07\DouKuaiOpenApi\JinRiTeMai
 *
 * @property \Tb07\DouKuaiOpenApi\JinRiTeMai\Alliance\Auth\Client $alliance_access_token
 */
class Application extends ServiceContainer
{
    protected $baseUri   = 'https://openapi-fxg.jinritemai.com/';//正式环境
    protected $providers = [
        Alliance\Auth\AllianceAuthProvider::class,
    ];

    public function getAppKey()
    {
        return $this->getConfig('app_key');
    }

    public function getAppSecret()
    {
        return $this->getConfig('app_secret');
    }

    public function getServiceId()
    {
        return $this->getConfig('service_id');
    }

    public function setAccessToken($accessToken)
    {
        $config = $this->getConfig();
        $config = array_merge($config, ['access_token' => $accessToken]);
        $this->setConfig($config);
    }

    public function getAccessToken()
    {
        return $this->getConfig('access_token');
    }

    public function getBaseUri()
    {
        return $this->baseUri;
    }

    public function setBaseUri($baseUri)
    {
        return $this->baseUri = $baseUri;
    }
}