<?php

namespace DouKuaiOpenApi\Tests\Unit;


use DouKuaiOpenApi\Tests\TestCase;
use Tb07\DouKuaiOpenApi\Factory;

class DouDian extends TestCase
{
    protected $accessToken = 'b30e0a8d-4049-4739-962b-0889bf8d2cfe';

    public function testInvestmentActivityOpenList()
    {

//        $jinRiTeMai=Factory::jinRiTeMai($this->getConfig());
//        $jinRiTeMai->alliance_access_token;
//        $params = ['product_id' => ''];
////        $response = $service->Shop->getProductListV2([]);
//        $result = $this->getApp()->product->getProduct($params);
//        print_r($result);
//        exit;
        $result = [];
        $this->assertOk($result);
    }

}