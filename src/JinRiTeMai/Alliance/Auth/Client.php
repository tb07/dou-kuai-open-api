<?php


namespace Tb07\DouKuaiOpenApi\JinRiTeMai\Alliance\Auth;

use Tb07\DouKuaiOpenApi\JinRiTeMai\Kernel\BaseClient;

/**
 * 达人授权
 * Class Client
 * @package Tb07\DouKuaiOpenApi\JinRiTeMai\Alliance\User
 */
class Client extends BaseClient
{
    protected $host = 'https://open.douyin.com';

    /**
     * 达人授权地址 URL.
     * @see https://developer.open-douyin.com/docs/resource/zh-CN/dop/develop/openapi/account-permission/douyin-get-permission-code
     * @param string $state
     * @param string $scope
     * @param string $redirect_uri
     * @return string
     */
    public function generateAuthUrl(string $state, string $scope, string $redirect_uri)
    {
        $url   = $this->host . '/platform/oauth/connect';
        $query = [
            'client_key'    => $this->application->getAppKey(),
            'response_type' => 'code',
            'scope'         => $scope,
            'redirect_uri'  => $redirect_uri,
            'state'         => $state,
            't'             => time(),
        ];
        return $url . '?' . http_build_query($query);
    }

    /**
     *  请求获取达人 access_token.
     * @see  https://developer.open-douyin.com/docs/resource/zh-CN/dop/develop/openapi/account-permission/get-access-token
     * @param string $code
     * @return mixed|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function requestAccessToken(string $code)
    {
        $params   = [
            'client_secret' => $this->application->getAppSecret(),
            'client_key'    => $this->application->getAppKey(),
            'code'          => $code,
            'grant_type'    => 'authorization_code',
        ];
        $headers  = $this->headers();
        $options  = [
            'json' => $params,
        ];
        $endpoint = '/oauth/access_token/';
        $url      = $this->host . $endpoint;
        $result   = $this->cusRequest('post', $url, $options, $headers);
        return $result;
    }

    /**
     * 刷新达人 access_token
     *
     * @see https://developer.open-douyin.com/docs/resource/zh-CN/dop/develop/openapi/account-permission/refresh-token
     * @param string $refresh_token
     * @return mixed|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function refreshAccessToken(string $refresh_token)
    {
        $params   = [
            'client_key'    => $this->application->getAppKey(),
            'refresh_token' => $refresh_token,
            'grant_type'    => 'refresh_token',
        ];
        $options  = [
            'json' => $params,
        ];
        $endpoint = '/oauth/refresh_token/';
        $url      = $this->host . $endpoint;
        $result   = $this->cusRequest('post', $url, $options);
        return $result;
    }

    /**
     *  生成应用 client_token
     * @see https://developer.open-douyin.com/docs/resource/zh-CN/dop/develop/openapi/account-permission/client-token
     * @return mixed|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getClientToken()
    {
        $params = [
            'client_key'    => $this->application->getAppKey(),
            'client_secret' => $this->application->getAppSecret(),
            'grant_type'    => 'client_credential',
        ];

        $options  = [
            'json' => $params,
        ];
        $endpoint = '/oauth/client_token/';
        $url      = $this->host . $endpoint;
        $result   = $this->cusRequest('post', $url, $options);
        return $result;
    }


    /**
     *  获取应用 jsb_ticket
     * @see https://developer.open-douyin.com/docs/resource/zh-CN/dop/develop/openapi/tools-ability/jsb-management/get-jsb-ticket
     * @param string $clientToken
     * @return mixed|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getJsGetticket(string $clientToken)
    {
        $headers = $this->headers();

        $headers['access-token'] = $clientToken;

        $endpoint = '/js/getticket/';
        $url      = $this->host . $endpoint;
        $options  = [
            'header' => $headers,
        ];
        $result   = $this->cusRequest('get', $url, $options);
        return $result;
    }

    protected function headers()
    {
        return [
            'Content-Type' => 'application/json',
        ];
    }

}
