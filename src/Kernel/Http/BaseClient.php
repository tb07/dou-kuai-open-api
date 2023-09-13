<?php

namespace Tb07\DouKuaiOpenApi\Kernel\Http;

use GuzzleHttp\Client as HttpClient;
use Psr\Http\Message\ResponseInterface;
use  Tb07\DouKuaiOpenApi\Kernel\Log\LogManager;
use Tb07\DouKuaiOpenApi\Kernel\Exception\HttpException;

/**
 * Class Http.
 */
class BaseClient
{
    /**
     * Http client.
     *
     * @var HttpClient
     */
    protected $client;

    /**
     * Guzzle client default settings.
     * @var array[]
     */
    protected static $defaults = [
        'curl' => [
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        ],
    ];

    /**
     * @param $url
     * @param array $query
     * @param array $headers
     * @param false $returnRaw
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get($url, array $query = [], array $headers = [], $returnRaw = false)
    {
        return $this->request('get', $url, [
            'headers' => $headers,
            'query'   => $query,
        ], $returnRaw
        );
    }

    /**
     * @param $url
     * @param array $params
     * @param array $headers
     * @param false $returnRaw
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function post($url, array $params = [], array $headers = [], $returnRaw = false)
    {
        return $this->request('post', $url, [
            'header'      => $headers,
            'form_params' => $params,
        ], $returnRaw
        );
    }

    /**
     * @param $url
     * @param array $params
     * @param array $headers
     * @param false $returnRaw
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function postJosn($url, array $params = [], array $headers = [], $returnRaw = false)
    {
        return $this->request('post', $url, [
            'header' => $headers,
            'json'   => $params,
        ], $returnRaw
        );
    }

    /**
     * @param $method
     * @param $url
     * @param array $options
     * @param false $returnRaw
     * @return mixed|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request($method, $url, array $options = [], $returnRaw = false)
    {
        $method = strtolower($method);
        LogManager::debug('Client Request:', compact('url', 'method', 'options'));
        $response = $this->getClient()->{$method}($url, $options);
        LogManager::debug('API response:', [
            'Status'  => $response->getStatusCode(),
            'Reason'  => $response->getReasonPhrase(),
            'Headers' => $response->getHeaders(),
            'Body'    => strval($response->getBody()),
        ]);
        return $this->unwrapResponse($response, $returnRaw);
    }

    /**
     * Upload file.
     *
     * @param string $url
     * @param array $files
     * @param array $form
     * @param array $queries
     *
     * @return ResponseInterface
     */
    public function upload($url, array $queries = [], array $files = [], array $form = [])
    {
        $multipart = [];

        foreach ($files as $name => $path) {
            if (is_array($path)) {
                foreach ($path as $item) {
                    $multipart[] = [
                            'name' => $name . '[]',
                        ] + $this->fileToMultipart($item);
                }
            } else {
                $multipart[] = [
                        'name' => $name,
                    ] + $this->fileToMultipart($path);
            }
        }

        foreach ($form as $name => $contents) {
            $multipart = array_merge($multipart, $this->normalizeMultipartField($name, $contents));
        }

        return $this->request('POST', $url, ['query' => $queries, 'multipart' => $multipart]);
    }

    /**
     * @param string $name
     * @param mixed $contents
     *
     * @return array
     */
    public function normalizeMultipartField(string $name, $contents)
    {
        $field = [];
        if (!is_array($contents)) {
            return [compact('name', 'contents')];
        } else {
            foreach ($contents as $key => $value) {
                $key   = sprintf('%s[%s]', $name, $key);
                $field = array_merge($field, is_array($value) ? $this->normalizeMultipartField($key, $value) : [
                    [
                        'name' => $key, 'contents' => $value,
                    ],
                ]);
            }
        }
        return $field;
    }

    private function fileToMultipart($file)
    {
        if (is_array($file)) {
            return $file;
        } elseif (@file_exists($file)) {
            return ['contents' => fopen($file, 'r')];
        } elseif (filter_var($file, FILTER_VALIDATE_URL)) {
            return ['contents' => file_get_contents($file)];
        } else {
            return ['contents' => $file];
        }
    }


    /**
     * Return GuzzleHttp\Client instance.
     *
     * @return \GuzzleHttp\Client
     */
    public function getClient()
    {
        if (!($this->client instanceof HttpClient)) {
            $this->client = new HttpClient();
        }
        return $this->client;
    }

    /**
     * 统一转换响应结果为 json 格式.
     * @param ResponseInterface $response
     * @param $returnRaw
     * @return mixed|\Psr\Http\Message\StreamInterface
     */
    protected function unwrapResponse(ResponseInterface $response, $returnRaw)
    {
        $contentType = $response->getHeaderLine('Content-Type');
        $contents    = $response->getBody();
        if ($returnRaw) {
            return $contents;
        }
        if (false !== stripos($contentType, 'json') || stripos($contentType, 'javascript')) {
            return json_decode($contents, true);
        } elseif (false !== stripos($contentType, 'xml')) {
            return json_decode(json_encode(simplexml_load_string($contents)), true);
        }
        return $contents;
    }
}
