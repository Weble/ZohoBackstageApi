<?php


namespace Weble\ZohoBackstageApi\Modules;


class Portals extends Module
{

    protected $availableModules = [

    ];

    public function details()
    {
        return $this->client->call('/eventMetaDetails', 'GET');
    }

    public function getName(): string
    {
        return 'portals';
    }

    protected function getResourceKey(): string
    {
        return 'portal';
    }
}