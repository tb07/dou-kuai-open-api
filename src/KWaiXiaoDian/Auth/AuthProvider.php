<?php


namespace Tb07\DouKuaiOpenApi\KWaiXiaoDian\Auth;

use Pimple\ServiceProviderInterface;
use Pimple\Container;

/**
 * 应用授权
 * Class AuthProvider
 * @package Tb07\DouKuaiOpenApi\KWaiXiaoDian\Auth
 */
class AuthProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['access_token'] = function ($container) {
            return new Client($container);
        };
    }

}
