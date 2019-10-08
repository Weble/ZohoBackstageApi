<?php

namespace Weble\ZohoBackstageApi;

use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Client
 * @see https://github.com/opsway/zohobooks-api
 * @package Webleit\ZohoBooksApi
 */
class Client
{
    const ROOT_URI = '/backstage/public/';

    /**
     * @var Client[]
     */
    protected static $intances = [];

    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    private function __construct(string $url)
    {
        $this->client = new \GuzzleHttp\Client([
            'base_uri' => $url
        ]);
    }

    public function getBaseUri(): string
    {
        return $this->client->getConfig('base_uri');
    }

    public static function getInstance(string $url = null): self
    {
        if ($url === null) {
            $availableClients = array_keys(self::$intances);
            $url = array_shift($availableClients);
        }

        if (!isset(self::$intances[$url])) {
            self::$intances[$url] = new self($url);
        }

        return self::$intances[$url];
    }

    public function call(string $uri, string $method, array $data = [])
    {
        $options = array_merge([
            'query' => [],
            'form_params' => [],
            'json' => [],
            'headers' => []
        ], $data);

        try {
            return $this->client->$method($uri, $options);
        } catch (ClientException $e) {

            $response = $e->getResponse();

            if (!$response) {
                throw $e;
            }

            $response = json_decode($response->getBody());
            if (!$response) {
                throw $e;
            }

            if (!isset($response->code)) {
                throw $e;
            }

            throw $e;
        }
    }

    public function getList($uri, $params = [])
    {

        $response = $this->call($uri, 'GET', ['query' => $params]);

        $body = $response->getBody();

        $data = json_decode($body, true);

        return $data;
    }

    /**
     * @param $url
     * @param  null  $id
     * @param  array  $params
     *
     * @return array|mixed|string
     * @throws ApiError
     * @throws GrantCodeNotSetException
     */
    public function get($url, $id = null, $params = [])
    {
        if ($id !== null) {
            $url .= '/'.$id;
        }

        return $this->processResult(
            $this->call($url, 'GET', ['query' => $params])
        );
    }

    public function post($url, $params = [], $queryParams = [])
    {
        return $this->processResult(
            $this->call($url, 'POST', [
                'query' => $queryParams,
                'json' => $params
            ])
        );
    }

    /**
     * @param $url
     * @param  array  $params
     * @param  array  $queryParams
     *
     * @return array|mixed|string
     * @throws \Webleit\ZohoCrmApi\Exception\ApiError
     * @throws \Webleit\ZohoCrmApi\Exception\GrantCodeNotSetException
     * @throws \Webleit\ZohoCrmApi\Exception\NonExistingModule
     */
    public function put($url, $params = [], $queryParams = [])
    {
        return $this->processResult(
            $this->call($url, 'PUT', [
                'query' => $queryParams,
                'json' => $params
            ])
        );
    }

    /**
     * @param  ResponseInterface  $response
     *
     * @return array|mixed|string
     * @throws ApiError
     */
    public function processResult(ResponseInterface $response)
    {
        // All ok, probably not json, like PDF?
        if ($response->getStatusCode() < 200 || $response->getStatusCode() > 299) {
            throw new ApiError('Response from Zoho is not success. Message: '.$response->getReasonPhrase());
        }

        try {
            $result = json_decode($response->getBody(), true);
        } catch (\InvalidArgumentException $e) {

            // All ok, probably not json, like PDF?
            if ($response->getStatusCode() >= 200 && $response->getStatusCode() <= 299) {
                return (string) $response->getBody();
            }

            throw new ApiError('Response from Zoho is not success. Message: '.$response->getReasonPhrase());
        }

        if (!$result) {
            // All ok, probably not json, like PDF?
            if ($response->getStatusCode() >= 200 && $response->getStatusCode() <= 299) {
                return (string) $response->getBody();
            }

            throw new ApiError('Response from Zoho is not success. Message: '.$response->getReasonPhrase());
        }

        return $result;
    }

    /**
     * @return \GuzzleHttp\Client
     */
    public function getHttpClient(): \GuzzleHttp\Client
    {
        return $this->client;
    }
}