<?php

namespace Webleit\ZohoCrmApi\Test;

use PHPUnit\Framework\TestCase;
use Weble\ZohoBackstageApi\Builders\OrderBuilder;
use Weble\ZohoBackstageApi\Client;
use Weble\ZohoBackstageApi\Models\Attendee;
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

    /**
     * @test
     */
    public function canGetSingleEvent()
    {
        /** @var Portal $portal */
        $portal = self::$zoho->portals->getList()->first();
        /** @var Event $event */
        $event = $portal->events->getList()->first();
        $fetchedEvent = $portal->events->get($event->getId());

        $this->assertTrue($fetchedEvent instanceof Event);
        $this->assertEquals($event->getId(), $fetchedEvent->getId());
    }

    /**
     * @test
     */
    public function canGetOrder()
    {
        /** @var Portal $portal */
        $portal = self::$zoho->portals->getList()->first();

        $order = $portal->orders->get('433000000100477');

        dd($order->toArray());

        $this->assertEquals(Order::class, get_class($order));
        $this->assertArrayHasKey('status', $order->toArray());
        $this->assertArrayHasKey('orderTickets', $order->toArray());
    }

    /**
     * @test
     */
    public function canCreateOrder()
    {
        /** @var Portal $portal */
        $portal = self::$zoho->portals->getList()->first();

        /** @var Event $event */
        $event = $portal->events->getList()->first();

        $order = (new OrderBuilder())
            ->forEvent($event)
            ->boughtBy('test@example.com')
            ->assignTo('test@example.com');

        $createdOrder = $portal->orders->create($order);

        $this->assertEquals(Order::class, get_class($createdOrder));
        $this->assertArrayHasKey('status', $createdOrder->toArray());
        $this->assertArrayHasKey('attendees', $createdOrder->toArray());
        $this->assertIsArray($createdOrder->attendees);

        $this->assertGreaterThan(0, count($createdOrder->attendees));

        foreach ($createdOrder->attendees as $attendee) {
            $this->assertEquals(Attendee::class, get_class($attendee));
            $this->assertArrayHasKey('emailId', $attendee->toArray());
            $this->assertArrayHasKey('ticketId', $attendee->toArray());
            $this->assertArrayHasKey('orderTicket', $attendee->toArray());
        }
    }
}