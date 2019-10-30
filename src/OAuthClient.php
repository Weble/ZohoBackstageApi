<?php

namespace Weble\ZohoBackstageApi;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Weble\ZohoBackstageApi\Exceptions\ErrorResponseException;
use Weble\ZohoClient\OAuthClient as ZohoOAuthClient;

class OAuthClient
{
    const ENDPOINT_CN = 'https://backstage.zoho.com.cn/';
    const ENDPOINT_EU = 'https://backstage.zoho.eu/';
    const ENDPOINT_IN = 'https://backstage.zoho.in/';
    const ENDPOINT_US = 'https://backstage.zoho.com/';

    const API_V0 = '';
    const API_V1 = 'v1';
    const DEFAULT_API_VERSION = self::API_V1;

    /** @var self */
    protected static $instance;

    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @var ZohoOAuthClient
     */
    protected $oAuthClient;

    /**
     * @var string
     */
    protected $region = ZohoOAuthClient::DC_US;

    /**
     * @var string
     */
    protected $apiVersion;

    /**
     * Client constructor.
     * @param $clientId
     * @param $clientSecret
     * @param $refreshToken
     */
    private function __construct($clientId, $clientSecret, $refreshToken = null)
    {
        $this->createClient();

        $this->oAuthClient = new ZohoOAuthClient($clientId, $clientSecret, $refreshToken);
        $this->oAuthClient->setRefreshToken($refreshToken);
    }

    public function setApiVersion(string $apiVersion): self
    {
        $this->apiVersion = $apiVersion;
        $this->createClient();
        return $this;
    }

    /**
     * @param  string  $region
     * @return $this
     */
    public function setRegion($region = ZohoOAuthClient::DC_US)
    {
        $this->region = $region;
        $this->oAuthClient->euRegion();
        $this->createClient();

        return $this;
    }

    /**
     * @return Client|string
     */
    protected function createClient()
    {
        $this->httpClient = new Client(['base_uri' => $this->getEndPoint(), 'http_errors' => false]);
        return $this->httpClient;
    }

    /**
     * @return string
     */
    public function getEndPoint()
    {
        $apiVersionSuffix = $this->apiVersion ? $this->apiVersion.'/' : '';

        switch ($this->region) {
            case ZohoOAuthClient::DC_CN:
                return self::ENDPOINT_CN.$apiVersionSuffix;
                break;
            case ZohoOAuthClient::DC_IN:
                return self::ENDPOINT_IN.$apiVersionSuffix;
                break;
            case ZohoOAuthClient::DC_EU:
                return self::ENDPOINT_EU.$apiVersionSuffix;
                break;
            case ZohoOAuthClient::DC_US:
            default:
                return self::ENDPOINT_US.$apiVersionSuffix;
                break;
        }
    }

    /**
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    public function getList($url, array $filters = [])
    {
        return $this->processResult(
            $this->httpClient->get($url, $this->getOptions(['query' => $filters]))
        );
    }

    public function get($url, $id = null, array $params = [])
    {
        if ($id !== null) {
            $url .= '/'.$id;
        }

        return $this->processResult(
            $this->httpClient->get($url, $this->getOptions(['query' => $params]))
        );
    }

    public function rawGet($url, array $params = [])
    {
        try {
            $response = $this->httpClient->get($url, $this->getOptions(['query' => $params]));
            return $response->getBody();
        } catch (\InvalidArgumentException $e) {
            throw new ErrorResponseException('Response from Zoho is not success. Message: '.$e);
        }
    }

    public function rawPost($url, array $params)
    {
        return $this->processResult($this->httpClient->post(
            $url,
            $this->getOptions($params)
        ));
    }

    public function post($url, array $data = [], array $params = [])
    {
        return $this->processResult($this->httpClient->post(
            $url,
            $this->getOptions([
                'query' => $params,
                'form_data' => $data
            ])
        ));
    }

    public function put($url, $id = null, array $data = [], array $params = [])
    {
        if ($id !== null) {
            $url .= '/'.$id;
        }

        return $this->processResult($this->httpClient->put(
            $url,
            $this->getOptions([
                'query' => $params,
            ])
        ));
    }

    public function delete($url, $id = null)
    {
        if ($id !== null) {
            $url .= '/'.$id;
        }

        return $this->processResult(
            $this->httpClient->delete($url)
        );
    }

    protected function getOptions($params = [])
    {
        return array_merge([
            'headers' => [
                'Authorization' => 'Zoho-oauthtoken '.$this->oAuthClient->getAccessToken()
            ]
        ], $params);
    }


    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->oAuthClient, $name], $arguments);
    }

    public function processResult(ResponseInterface $response)
    {
        try {
            $result = json_decode($response->getBody(), true);
        } catch (\InvalidArgumentException $e) {

            // All ok, probably not json, like PDF?
            if ($response->getStatusCode() >= 200 && $response->getStatusCode() <= 299) {
                return (string) $response->getBody();
            }

            $result = [
                'message' => 'Internal API error: '.$response->getStatusCode().' '.$response->getReasonPhrase(),
            ];
        }

        if (!$result) {
            // All ok, probably not json, like PDF?
            if ($response->getStatusCode() >= 200 && $response->getStatusCode() <= 299) {
                return (string) $response->getBody();
            }

            $result = [
                'message' => 'Internal API error: '.$response->getStatusCode().' '.$response->getReasonPhrase(),
            ];
        }

        dump($this->oAuthClient->getAccessToken());

        if (!isset($result['error'])) {
            return $result;
        }


        throw new ErrorResponseException('Response from Zoho is not success. Message: '.$result['error']);
    }

    public static function getInstance($clientId = null, $clientSecret = null, $refreshToken = null): self
    {
        if (!self::$instance) {
            self::$instance = new self($clientId, $clientSecret, $refreshToken);
        }

        return self::$instance;
    }
}
