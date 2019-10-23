<?php

namespace Weble\ZohoBackstageApi\Models;

use Tightenco\Collect\Contracts\Support\Arrayable;
use Weble\ZohoBackstageApi\Client;
use Weble\ZohoBackstageApi\Mixins\ProvidesModules;
use Weble\ZohoBackstageApi\OAuthClient;

class Model implements \JsonSerializable, Arrayable
{
    use ProvidesModules;

    protected $availableModules = [

    ];

    /**
     * @var array
     */
    protected $data = [];

    protected $casts = [];

    /** @var OAuthClient */
    protected $oAuthClient;

    /** @var string string */
    protected $baseUrl = '';

    public function __construct($data = [], $baseUrl = null)
    {
        $this->baseUrl = $baseUrl;

        $this->data = $data;
        $this->oAuthClient = OAuthClient::getInstance();

        $castedFields = array_keys($this->casts);
        if (count($castedFields) > 0) {
            foreach ($this->data as $key => $value) {
                if (in_array($key, $castedFields)) {
                    switch ($this->casts[$key]) {
                        case 'datetime':
                        case 'date':
                            $timezone = array_keys($this->casts, 'timezone');
                            if ($timezone) {
                                $timezone = array_shift($timezone);
                            }
                            $this->data[$key] = (new \DateTime($value));

                            if ($timezone && isset($this->data[$timezone])) {
                                $this->data[$key]->setTimeZone(new \DateTimeZone($this->data[$timezone]));
                                unset($this->data[$timezone]);
                            }
                            break;
                    }
                }
            }
        }
    }

    public function __get($name)
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }

        if ($this->hasModule($name)) {
            return $this->createModule($name)->setBaseUrlForEndpoint($this->baseUrl);
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