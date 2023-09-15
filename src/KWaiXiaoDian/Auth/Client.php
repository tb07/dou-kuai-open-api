<?php


namespace Tb07\DouKuaiOpenApi\KWaiXiaoDian\Auth;

use Tb07\DouKuaiOpenApi\KWaiXiaoDian\Kernel\BaseClient;

/**
 * 应用授权
 * Class Client
 * @package Tb07\DouKuaiOpenApi\JinRiTeMai\Alliance\User
 */
class Client extends BaseClient
{
    //pc 端授权域名
    protected $pcAuthUrl = 'https://open.kwaixiaodian.com';
    //app端授权域名
    protected $appAuthUrl = 'https://open.kuaishou.com';

    /**
     * 创建pc 授权地址
     * @see https://open.kwaixiaodian.com/docs/dev?pageSign=e1d9e229332f4f233a04b44833a5dfe71614263940720#section-8
     * @param string $redirectUri 授权成功后的回调地址，必须以http/https开头。和注册时的回调地址保持schema和子域名一致
     * @param array|string[] $scope 作用域
     * @param string $state 安全参数，标识和用户或者设备相关的授权请求。建议开发者实现。回调的时候会带回
     * @param string $responseType 写死 code
     * @return string
     */
    public function createPcAuthUrl(string $redirectUri, array $scope = ['user_info'], string $state = 'code', string $responseType = 'code')
    {
        $scope   = implode(',', $scope);
        $authUrl = "{$this->pcAuthUrl}/oauth/authorize?app_id={$this->application->getAppKey()}&redirect_uri={$redirectUri}&scope={$scope}&response_type={$responseType}&state={$state}";
        return $authUrl;
    }

    /**
     * 创建快手 app 授权地址
     * @see https://open.kwaixiaodian.com/docs/dev?pageSign=e1d9e229332f4f233a04b44833a5dfe71614263940720#section-5
     * @param string $redirectUri 授权成功后的回调地址，必须以http/https开头。和注册时的回调地址保持schema和子域名一致
     * @param array|string[] $scope 作用域
     * @param string $state 安全参数，标识和用户或者设备相关的授权请求。建议开发者实现。回调的时候会带回。
     * @param string $responseType 写死 code
     * @return string
     */
    public function createAppAuthUrl(string $redirectUri, array $scope = ['user_info'], string $state = 'code', string $responseType = 'code')
    {
        $scope   = implode(',', $scope);
        $authUrl = "{$this->appAuthUrl}/oauth2/authorize?app_id={$this->application->getAppKey()}&redirect_uri={$redirectUri}&scope={$scope}&response_type={$responseType}&state={$state}";
        return $authUrl;
    }


    /**
     * 获取授权  访问令牌accessToken
     * @see https://open.kwaixiaodian.com/docs/dev?pageSign=e1d9e229332f4f233a04b44833a5dfe71614263940720#section-11
     * @param string $code 2.2中获取到的code
     * @param string $grantType 授权的类型，"code"
     * @return mixed
     */
    public function requestAccessToken(string $code, string $grantType = 'code')
    {
        $query    = [
            'app_id'     => $this->application->getAppKey(),
            'app_secret' => $this->application->getAppSecret(),
            'grant_type' => $grantType,
            'code'       => $code,
        ];
        $options  = [
            'query' => $query,
        ];
        $endpoint = '/oauth2/access_token/';
        $url      = $this->application->getBaseUri() . $endpoint;
        $result   = $this->cusRequest('get', $url, $options);
        return $result;
    }


    /**
     * 刷新授权凭证
     * @see https://open.kwaixiaodian.com/docs/dev?pageSign=e1d9e229332f4f233a04b44833a5dfe71614263940720#section-13
     * @param string $refreshToken 长时访问令牌，默认为180天，2.3接口中返回的值
     * @param string $grantType 授权的类型，必须是"refresh_token"
     * @return mixed
     */
    public function refreshToken(string $refreshToken, string $grantType = 'refresh_token')
    {
        $params   = [
            'app_id'        => $this->application->getAppKey(),
            'app_secret'    => $this->application->getAppSecret(),
            'grant_type'    => $grantType,
            'refresh_token' => $refreshToken,
        ];
        $options  = [
            'form_params' => $params,
        ];
        $endpoint = '/oauth2/refresh_token/';
        $url      = $this->application->getBaseUri() . $endpoint;
        $result   = $this->cusRequest('post', $url, $options);
        return $result;
    }

    /**
     *  生成应 client_credentials 调用非授权open api 接口凭证
     * @see https://open.kwaixiaodian.com/docs/dev?pageSign=e1d9e229332f4f233a04b44833a5dfe71614263940720#section-13
     * @return mixed
     */
    public function getClientToken()
    {
        $query    = [
            'app_id'     => $this->application->getAppKey(),
            'app_secret' => $this->application->getAppSecret(),
            'grant_type' => 'client_credentials',
        ];
        $options  = [
            'query' => $query,
        ];
        $endpoint = '/oauth2/access_token/';
        $url      = $this->application->getBaseUri() . $endpoint;
        $result   = $this->cusRequest('get', $url, $options);
        return $result;
    }

}
