<?php

namespace Tb07\DouKuaiOpenApi\JinRiTeMai\Kernel;


use Tb07\DouKuaiOpenApi\Kernel\Http\BaseClient as douBaseClient;
use Tb07\DouKuaiOpenApi\JinRiTeMai\Application;

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
        $query = $this->generateParams($endpoint, $query);
        $url   = $this->application->getBaseUri() . $endpoint;
        return parent::get($url, $query, $headers, $returnRaw);
    }

    public function post($endpoint, array $params = [], array $headers = [], $returnRaw = false)
    {
        $params = $this->generateParams($endpoint, $params);
        $url    = $this->application->getBaseUri() . $endpoint;
        return parent::get($url, $params, $headers, $returnRaw);
    }

    public function postJosn($endpoint, array $params = [], array $headers = [], $returnRaw = false)
    {
        $params = $this->generateParams($endpoint, $params);
        $url    = $this->application->getBaseUri() . $endpoint;
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
        return $this->unwrapResponse(parent::request($method, $url, $options), $returnRaw);
    }

    /**
     * 组合公共参数、业务参数.
     *
     * @see https://op.jinritemai.com/docs/guide-docs/10/23
     *
     * @param string $url 支持 /shop/brandList 或者 shop/brandList 格式
     * @param array $params 业务参数
     */
    public function generateParams(string $endpoint, array $params, $isAccessToken = true)
    {
        $method = ltrim(str_replace('/', '.', $endpoint), '.');
        //公共参数
        $publicParams = [
            'method'      => $method,
            'app_key'     => $this->application->getAppKey(),
            'timestamp'   => date('Y-m-d H:i:s'),
            'v'           => '2',
            'sign_method' => 'md5',
        ];
        if ($isAccessToken) {
            $publicParams['access_token'] = $this->application->getAccessToken();
        }
        //业务参数
        $params      = $this->ksort($params);
        $params_json = str_replace("\b", '\u0008', json_encode((object)$params, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP));

        $string = 'app_key' . $publicParams['app_key'] . 'method' . $method . 'param_json' . $params_json . 'timestamp' . $publicParams['timestamp'] . 'v' . $publicParams['v'];
        $md5Str = $this->application->getAppSecret() . $string . $this->application->getAppSecret();
        $sign   = md5($md5Str);

        return array_merge($publicParams, [
            'param_json' => $params_json,
            'sign'       => $sign,
        ]);
    }

    /**
     * 将数组中的每个数组元素按照key自然排序
     *
     * @param array $array
     * @param int $rule
     * @return array
     */
    protected function ksort(array $array = [], int $rule = SORT_NATURAL)
    {
        $stack = [&$array];
        for ($count = 1, $first = true; $count > 0; $first = true) {
            ksort($stack[$count - 1], $rule);
            foreach ($stack[--$count] as &$val) {
                if ($first === true) {
                    $first = false;
                    array_pop($stack);
                }
                if (is_array($val)) {
                    $stack[] = &$val;
                    $count++;
                }
            }
        }
        return $array;
    }
}