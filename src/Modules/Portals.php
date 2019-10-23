<?php


namespace Weble\ZohoBackstageApi\Modules;


class Portals extends Module
{

    public function getName(): string
    {
        return 'portals';
    }

    protected function getResourceKey(): string
    {
        return 'portals';
    }
}