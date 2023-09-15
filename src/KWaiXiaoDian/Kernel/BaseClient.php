<?php

namespace Tb07\DouKuaiOpenApi\KWaiXiaoDian\Kernel;


use Tb07\DouKuaiOpenApi\Kernel\Http\BaseClient as douBaseClient;
use Tb07\DouKuaiOpenApi\KWaiXiaoDian\Application;

class BaseClient extends douBaseClient
{
    /** @var Application */
    protected $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function get($endpoint, array $query = [], array $headers = [], $returnRaw = false)
    {
        $query   = $this->generateParams($endpoint, $query);
        $routing = '/' . strtr($endpoint, '.', '/');
        $url     = $this->application->getBaseUri() . $routing;
        return parent::get($url, $query, $headers, $returnRaw);
    }

    public function post($endpoint, array $params = [], array $headers = [], $returnRaw = false)
    {
        $params  = $this->generateParams($endpoint, $params);
        $routing = '/' . strtr($endpoint, '.', '/');
        $url     = $this->application->getBaseUri() . $routing;
        return parent::get($url, $params, $headers, $returnRaw);
    }

    public function postJosn($endpoint, array $params = [], array $headers = [], $returnRaw = false)
    {
        $params  = $this->generateParams($endpoint, $params);
        $routing = '/' . strtr($endpoint, '.', '/');
        $url     = $this->application->getBaseUri() . $routing;
        return parent::postJosn($url, $params, $headers, $returnRaw);
    }

    /**
     * 直接访问数据
     * @param $method
     * @param $url
     * @param array $options
     * @param array $headers
     * @param false $returnRaw
     * @return mixed|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function cusRequest($method, $url, $options = [], $returnRaw = false)
    {
        return parent::request($method, $url, $options, $returnRaw);
    }

    /**
     * 组合公共参数、业务参数.
     *
     * @see https://open.kwaixiaodian.com/docs/dev?pageSign=8cca5d25ba0015e5045a7ebec6383b741614263875756
     *
     * @param string $url 支持 /shop/brandList 或者 shop/brandList 格式
     * @param array $params 业务参数
     */
    public function generateParams(string $endpoint, array $params, $isAccessToken = true)
    {
        //公共参数
        $params = [
            'appkey'     => $this->application->getAppKey(),
            "timestamp"  => intval(microtime(true) * 1000),
            'version'    => 1,//请求的API版本号，目前版本为1
            //            'access_token' => $accessToken,    //所有需用户授权API使用code模式获取，不需要用户授权API使用client_credentials获取，详情参考《授权说明》文档
            "param"      => (is_array($params) && !empty($params)) ? json_encode($params) : '{}',//JSON 业务参数
            'method'     => $endpoint,//请求方式
            'signMethod' => 'MD5', //支持HMAC_SHA256和MD5，推荐使用HMAC_SHA256
        ];
        if ($isAccessToken) {
            $params['access_token'] = $this->application->getAccessToken();
        }
        $params['sign'] = $this->generateSign($params, $this->application->getSignSecret());

        return $params;
    }

    protected function generateSign(array $attributes, string $signSecret)
    {
        $attributes = array_filter(
            $attributes,
            function ($value) {
                return !empty($value);
            }
        );
        ksort($attributes);

        $stringToBeSigned = '';
        foreach ($attributes as $key => $value) {
            if ($stringToBeSigned) {
                $stringToBeSigned .= "&$key=$value";
            } else {
                $stringToBeSigned .= "$key=$value";
            }
        }
        $stringToBeSigned .= "&signSecret={$signSecret}";
        unset($k, $v);
        return md5($stringToBeSigned);
    }
}