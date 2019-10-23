<?php

namespace Webleit\ZohoCrmApi\Test;

use PHPUnit\Framework\TestCase;
use Weble\ZohoBackstageApi\Client;
use Weble\ZohoBackstageApi\Models\Order;
use Weble\ZohoBackstageApi\Models\Event;
use Weble\ZohoBackstageApi\Models\EventMetaDetails;
use Weble\ZohoBackstageApi\Models\Portal;
use Weble\ZohoBackstageApi\Models\TicketAssignee;
use Weble\ZohoBackstageApi\Models\TicketBuyer;
use Weble\ZohoBackstageApi\Models\TicketClass;
use Weble\ZohoBackstageApi\Models\TicketContainer;
use Weble\ZohoBackstageApi\Models\Venue;
use Weble\ZohoBackstageApi\ZohoBackstage;
use Weble\ZohoClient\OAuthClient;


class V1ApiTest extends TestCase
{
    /**
     * @var Client
     */
    protected static $client;

    /**
     * @var ZohoBackstage
     */
    protected static $zoho;

    /**
     * setup
     */
    public static function setUpBeforeClass()
    {
        $authFile = __DIR__.'/config.example.json';
        if (file_exists(__DIR__.'/config.json')) {
            $authFile = __DIR__.'/config.json';
        }

        $auth = json_decode(file_get_contents($authFile));

        $zoho = new ZohoBackstage($auth->client_id, $auth->client_secret, $auth->refresh_token);
        $zoho->setRegion(OAuthClient::DC_EU);

        self::$zoho = $zoho;
    }

    /**
     * @test
     */
    public function canGetPortals()
    {
        $list = self::$zoho->portals->getList();

        $this->assertGreaterThan(0, $list->count());
        $list->each(function ($model) {
            $this->assertEquals(Portal::class, get_class($model));
            $this->assertArrayHasKey('id', $model->toArray());
            $this->assertArrayHasKey('name', $model->toArray());
        });
    }

    /**
     * @test
     */
    public function canGetEventDetailsForPortal()
    {
        /** @var Portal $portal */
        $portal = self::$zoho->portals->getList()->first();
        $events = $portal->events->getList();

        $this->assertGreaterThan(0, $events->count());

        /** @var Event $event */
        $event = $events->first();
        $this->assertEquals(Event::class, get_class($event));

        $this->assertArrayHasKey('id', $event->toArray());
        $this->assertArrayHasKey('startDate', $event->toArray());
        $this->assertArrayHasKey('endDate', $event->toArray());
        $this->assertArrayHasKey('category', $event->toArray());

        $this->assertEquals(\DateTime::class, get_class($event->startDate));
        $this->assertEquals(\DateTime::class, get_class($event->endDate));

        foreach ($event->translatedFields() as $field) {
            $this->assertArrayHasKey($field, $event->toArray());
            $this->assertIsArray($event->$field);
        }

        $this->assertEquals(Venue::class, get_class($event->venue));
    }

    /**
     * @test
     */
    public function canGetVenuesForPortal()
    {
        /** @var Portal $portal */
        $portal = self::$zoho->portals->getList()->first();
        $venues = $portal->venues->getList();

        $this->assertGreaterThan(0, $venues->count());

        /** @var Venue $venue */
        $venue = $venues->first();
        $this->assertEquals(Venue::class, get_class($venue));

        $this->assertArrayHasKey('id', $venue->toArray());
        $this->assertArrayHasKey('zipcode', $venue->toArray());
        $this->assertArrayHasKey('country', $venue->toArray());
        $this->assertArrayHasKey('latitude', $venue->toArray());
        $this->assertArrayHasKey('longitude', $venue->toArray());
        $this->assertArrayHasKey('country', $venue->toArray());
    }
}