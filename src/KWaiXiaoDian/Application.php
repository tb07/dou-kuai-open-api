<?php

namespace Tb07\DouKuaiOpenApi\KWaiXiaoDian;

use Tb07\DouKuaiOpenApi\Kernel\Core\ServiceContainer;


/**
 * 快手开发接口
 * Class Application
 * @package Tb07\DouKuaiOpenApi\KWaiXiaoDian
 * @property \Tb07\DouKuaiOpenApi\KWaiXiaoDian\Auth\Client $access_token
 */
class Application extends ServiceContainer
{
    protected $baseUri   = 'https://openapi.kwaixiaodian.com';//正式环境
    protected $providers = [
        Auth\AuthProvider::class,
    ];

    public function getAppKey()
    {
        return $this->getConfig('app_key');
    }

    public function getAppSecret()
    {
        return $this->getConfig('app_secret');
    }

    public function getSignSecret()
    {
        return $this->getConfig('sign_secret');
    }

    public function getMessageSecretKey()
    {
        return $this->getConfig('msg_secret_key');
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