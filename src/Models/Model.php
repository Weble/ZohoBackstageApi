<?php

namespace Weble\ZohoBackstageApi\Models;

use Tightenco\Collect\Contracts\Support\Arrayable;
use Weble\ZohoBackstageApi\Client;
use Weble\ZohoBackstageApi\Mixins\ProvidesModules;

abstract class Model implements \JsonSerializable, Arrayable
{
    use ProvidesModules;

    protected $availableModules = [

    ];

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var Client
     */
    protected $client;

    public function __construct($data = [], $baseUrl = null)
    {
        $baseUrl = Client::getInstance()->getBaseUri() . $baseUrl;

        $this->data = $data;
        $this->client = Client::getInstance($baseUrl);
    }

    abstract public function getName(): string;

    public function __get($name)
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }

        if ($this->hasModule($name)) {
            return $this->createModule($name);
        }
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->getData();
    }

    /**
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * is a new object?
     * @return bool
     */
    public function isNew()
    {
        return !$this->getId();
    }

    /**
     * Get the id of the object
     * @return bool|string
     */
    public function getId()
    {
        $key = $this->getKeyName();
        return $this->$key ? $this->$key : false;
    }

    /**
     * Get the name of the primary key
     */
    public function getKeyName()
    {
        return 'id';
    }
}