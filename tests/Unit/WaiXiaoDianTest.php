<?php

namespace DouKuaiOpenApi\Tests\Unit;


use DouKuaiOpenApi\Tests\TestCase;
use Tb07\DouKuaiOpenApi\Factory;
use Tb07\DouKuaiOpenApi\KWaiXiaoDian\Application;

class WaiXiaoDian extends TestCase
{
    protected $accessToken = 'b30e0a8d-4049-4739-962b-0889bf8d2cfe';

    public function testInvestmentActivityOpenList()
    {
        $Config       = [
            'debug'          => true,
            'app_key'        => 'ks712694643687773091',
            'app_secret'     => '0-99cU2o9EcMSl31eAfgKw',
            'service_id'     => getenv('open.service_id'),
            'sign_secret'    => '048df448a2c5e61080c6c51cf46c46c2',
            'msg_secret_key' => 'WoXdSaCV6LKlSiNAogG/1g==',
        ];
        $kwaixiaodian = new Application($Config);
        $kwaixiaodian = new Application($this->getConfig());
        $result       = $kwaixiaodian->access_token->getClientToken();
        $this->assertOk($result);
    }

}