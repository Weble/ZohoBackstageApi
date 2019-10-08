<?php


namespace Weble\ZohoBackstageApi\Modules;


use Tightenco\Collect\Support\Collection;
use Weble\ZohoBackstageApi\Client;
use Weble\ZohoBackstageApi\Models\Event;
use Weble\ZohoBackstageApi\Models\EventMetaDetails;

class Events extends Module
{
    const TYPE_LIVE = 'live';
    const TYPE_COMPLETED = 'completed';

    /** @var string  */
    private $type = self::TYPE_LIVE;

    public function __construct(Client $client)
    {
        parent::__construct($client);
    }

    public function getEndpoint(): string
    {
        return 'eventsMeta';
    }

    public function live(): Collection
    {
        return $this->getList([
            'type' => self::TYPE_LIVE
        ]);
    }

    public function completed(): Collection
    {
        return $this->getList([
            'type' => self::TYPE_COMPLETED
        ]);
    }

    public function getList($params = [])
    {
        if (!isset($params['type'])) {
            $params['type'] = self::TYPE_LIVE;
        }

        return parent::getList($params);
    }

    public function details(): EventMetaDetails
    {
        return new EventMetaDetails(
            $this->client->get('eventsMetaDetails')['meta']
        );
    }

    public function getName(): string
    {
        return 'portals';
    }

    protected function getResourceKey(): string
    {
        if ($this->type === self::TYPE_LIVE) {
            return 'liveEventMetas';
        }

        return 'completedEventsMetas';
    }

    public function getModelClassName(): string
    {
        return Event::class;
    }
}