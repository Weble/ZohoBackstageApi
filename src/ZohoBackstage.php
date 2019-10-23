<?php

namespace Weble\ZohoBackstageApi;


use Weble\ZohoBackstageApi\Mixins\ProvidesModules;
use Weble\ZohoBackstageApi\Modules\Portals;
use Weble\ZohoClient\OAuthClient as ZohoOAuthClient;

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
     * @var OAuthClient
     */
    private $oAuthClient;

    /**
     * @var array
     */
    protected $availableModules = [
        'portals' => Portals::class
    ];

    public function __construct($clientId, $clientSecret, $refreshToken = null)
    {
        $this->oAuthClient = OAuthClient::getInstance($clientId, $clientSecret, $refreshToken);
    }

    public function __get($name)
    {
        return $this->createModule($name);
    }

    public function setRegion($region = ZohoOAuthClient::DC_US): self
    {
        $this->oAuthClient->setRegion($region);
        return $this;
    }
}