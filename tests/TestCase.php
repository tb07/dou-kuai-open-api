<?php

namespace DouKuaiOpenApi\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;


class TestCase extends BaseTestCase
{
    protected $config;

    public function getConfig()
    {
        return $this->config = [
            'debug'          => true,
            'app_key'        => getenv('open.app_key'),
            'app_secret'     => getenv('open.app_secret'),
            'service_id'     => getenv('open.service_id'),
            'sign_secret'    => getenv('open.sign_secret'),
            'msg_secret_key' => getenv('open.msg_secret_key'),
        ];
    }

    public function assertOk(array $result)
    {
        if (!isset($result['error_msg']) || empty($result['error_msg'])) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false, $result['sub_msg'] ?? $result['error_msg'] ?? $result['error'] ?? '');
        }

    }
}