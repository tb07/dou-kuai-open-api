<?php

namespace Tb07\DouKuaiOpenApi\JinRiTeMai\Alliance\Auth;

use Pimple\ServiceProviderInterface;
use Pimple\Container;

/**
 * 达人授权服务
 * Class AllianceAuthProvider
 * @package Tb07\DouKuaiOpenApi\JinRiTeMai\Alliance\User
 */
class AllianceAuthProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['alliance_access_token'] = function ($container) {
            return new Client($container);
        };
    }

}
