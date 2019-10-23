<?php


namespace Weble\ZohoBackstageApi\Modules;


use Tightenco\Collect\Support\Collection;
use Weble\ZohoBackstageApi\Mixins\WithTranslationsData;
use Weble\ZohoBackstageApi\Models\Event;
use Weble\ZohoBackstageApi\Models\Venue;

class Venues extends Events
{
    use WithTranslationsData;

    public function getList($params = [])
    {
        return $this->venues();
    }
}