<?php

namespace Weble\ZohoBackstageApi\Modules;

use Tightenco\Collect\Support\Collection;
use Weble\ZohoBackstageApi\Client;
use Weble\ZohoBackstageApi\Models\Model;


abstract class Module
{
    /**
     * @var Client
     */
    protected $client;


    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    abstract public function getName(): string;

    public function getList($params = [])
    {
        $list = $this->client->getList($this->getEndpoint(), $params);

        $collection = new Collection($list[$this->getResourceKey()]);
        $collection = $collection->mapWithKeys(function ($item) {
            $item = $this->make($item);
            return [$item->getId() => $item];
        });

        return $collection;
    }

    public function get($id, array $params = [])
    {
        $item = $this->client->get($this->getUrl(), $id, $params);

        if (!is_array($item)) {
            return $item;
        }

        $data = array_shift($item[$this->getResourceKey()]);

        return $this->make($data);
    }

    public function getEndpoint(): string
    {
        return $this->getName();
    }

    protected function getResourceKey(): string
    {
        return strtolower($this->getName());
    }

    public function make($data = []): Model
    {
        $class = $this->getModelClassName();
        /** @var Model $model */
        return new $class($data, $this->getEndpoint() . '/' . $data['id'] . '/');
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function getModelClassName(): string
    {
        $class = '\\Weble\\ZohoBackstageApi\\Models\\'.ucfirst($this->getResourceKey());
        return $class;
    }
}
