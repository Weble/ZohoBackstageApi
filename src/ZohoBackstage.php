<?php

namespace Weble\ZohoBackstageApi;


use Weble\ZohoBackstageApi\Mixins\ProvidesModules;
use Weble\ZohoBackstageApi\Modules\Portals;

/**
 * Class ZohoBackstage
 * @package Weble\ZohoBackstageApi
 *
 * @property-read Portals $portals
 */
class ZohoBackstage
{
    use ProvidesModules;

    /**
     * @var string
     */
    private $url;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var array
     */
    private $availableModules = [
        'portals' => Portals::class
    ];

    public function __construct(string $url)
    {
        $this->url = $url;
        $this->client = Client::getInstance($url . Client::ROOT_URI);
    }

    public function __get($name)
    {
        return $this->createModule($name);
    }
}