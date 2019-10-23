<?php

namespace Weble\ZohoBackstageApi\Modules;

use Doctrine\Common\Inflector\Inflector;
use Tightenco\Collect\Support\Collection;
use Weble\ZohoBackstageApi\Models\Model;
use Weble\ZohoBackstageApi\OAuthClient;

abstract class Module
{
    /** @var OAuthClient */
    protected $oAuthClient;
    /**
     * @var string
     */
    protected $baseUrl;

    abstract public function getName(): string;

    public function __construct(OAuthClient $OAuthClient)
    {
        $this->oAuthClient = $OAuthClient;
    }

    public function setBaseUrlForEndpoint(string $url = ''): self
    {
        $this->baseUrl = $url;
        return $this;
    }

    public function getList($params = [])
    {
        $list = $this->oAuthClient->getList($this->baseUrl.$this->getEndpoint());

        return $this->createCollectionFromList($list, $this->getResourceKey());
    }

    protected function createCollectionFromList($list, $key = null, $class = null): Collection
    {
        $collection = new Collection($key ? $list[$key] ?? $list : $list);
        $collection = $collection->mapWithKeys(function ($item) use ($class) {
            $item = $this->make($item, $class);
            return [$item->getId() => $item];
        });

        return $collection;
    }

    public function get($id, array $params = [])
    {
        $item = $this->oAuthClient->get($this->getEndpoint(), $id, $params);

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

    public function make($data = [], $class = null): Model
    {
        if ($class === null) {
            $class = $this->getModelClassName();
        }

        /** @var Model $model */
        return new $class($data, $this->getEndpoint().'/'.$data['id'].'/');
    }

    public function getClient(): OAuthClient
    {
        return $this->oAuthClient;
    }

    public function getModelClassName(): string
    {
        $class = '\\Weble\\ZohoBackstageApi\\Models\\'.ucfirst(Inflector::singularize($this->getResourceKey()));
        return $class;
    }
}
