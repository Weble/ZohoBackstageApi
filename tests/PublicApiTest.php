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
use Weble\ZohoBackstageApi\ZohoBackstage;

/**
 * Class ClassNameGeneratorTest
 * @package Webleit\ZohoBooksApi\Test
 */
class PublicApiTest extends TestCase
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

        $zoho = new ZohoBackstage($auth->url);

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
        /** @var Portal $list */
        $portal = self::$zoho->portals->getList()->first();
        /** @var EventMetaDetails $details */
        $details = $portal->events->details();

        $this->assertEquals(EventMetaDetails::class, get_class($details));
        $this->assertGreaterThan(0, $details->cities()->count());
    }

    /**
     * @test
     */
    public function canGetSingleEvent()
    {
        /** @var Portal $portal */
        $portal = self::$zoho->portals->getList()->first();
        /** @var Event $event */
        $event = $portal->events->live()->first();
        $fetchedEvent = $portal->events->get($event->getId());

        $this->assertTrue($fetchedEvent instanceof Event);
        $this->assertEquals($event->getId(), $fetchedEvent->getId());
    }

    /**
     * @test
     */
    public function canGetLiveEventsForPortal()
    {
        /** @var Portal $list */
        $portal = self::$zoho->portals->getList()->first();
        $events = $portal->events->live();

        $this->assertGreaterThan(0, $events->count());
        $events->each(function ($model) {
            $this->assertEquals(Event::class, get_class($model));
            $this->assertArrayHasKey('id', $model->toArray());
        });
    }

    /**
     * @test
     */
    public function canGetCompletedEventsForPortal()
    {
        /** @var Portal $list */
        $portal = self::$zoho->portals->getList()->first();
        $events = $portal->events->completed();

        $this->assertEquals(0, $events->count());
    }

    /**
     * @test
     */
    public function canGetTicketsDetailsForEvent()
    {
        /** @var Portal $list */
        $portal = self::$zoho->portals->getList()->last();
        /** @var Event $event */
        $event = $portal->events->live()->first();

        $details = $event->tickets->details();

        $this->assertArrayHasKey('ticketContainers', $details->toArray());
        $this->assertArrayHasKey('eventTicketingLookups', $details->toArray());
        $this->assertArrayHasKey('ticketClasses', $details->toArray());
        $this->assertArrayHasKey('ticketClassTranslations', $details->toArray());
        $this->assertArrayHasKey('eventTicketMetaDetails', $details->toArray());
        $this->assertArrayHasKey('ticketSettings', $details->toArray());
        $this->assertArrayHasKey('eventTicketClassLookups', $details->toArray());
    }

    /**
     * @test
     */
    public function canGetTicketsContainersForEvent()
    {
        /** @var Portal $list */
        $portal = self::$zoho->portals->getList()->last();
        /** @var Event $event */
        $event = $portal->events->live()->first();

        $details = $event->tickets->ticketContainers();

        $this->assertIsBool($event->tickets->available());
        $this->assertGreaterThan(0, $details->count());
        $details->each(function ($model) {
            $this->assertEquals(TicketContainer::class, get_class($model));
            $this->assertArrayHasKey('id', $model->toArray());
        });
    }

    /**
     * @test
     */
    public function canGetTicketsClassForEvent()
    {
        /** @var Portal $list */
        $portal = self::$zoho->portals->getList()->last();
        /** @var Event $event */
        $event = $portal->events->live()->first();

        $details = $event->tickets->ticketClasses();

        $this->assertGreaterThan(0, $details->count());
        $details->each(function ($model) {
            $this->assertEquals(TicketClass::class, get_class($model));
            $this->assertArrayHasKey('id', $model->toArray());
            $this->assertArrayHasKey('quantity', $model->toArray());
            $this->assertArrayHasKey('unlimited', $model->toArray());
        });
    }

    /**
     * @test
     */
    public function canCreateOrder()
    {
        /** @var Portal $portal */
        $portal = self::$zoho->portals->getList()->first();

        $events = $portal->events->live();

        $event = null;
        foreach ($events as $e) {
            /** @var Event $event */
            if ($e->tickets->available()) {
                $event = $e;
                break;
            }
        }

        $this->assertNotNull($event);

        /** @var TicketClass $classes */
        $class = $event->tickets->ticketClasses()->first();
        $container = $event->tickets->ticketContainers()->first();

        $orderToBeCreated = new Order();
        $orderToBeCreated->setTicketContainer($container);
        $orderToBeCreated->addTicket($class);

        $order = $portal->orders->create($orderToBeCreated);
        $this->assertEquals(Order::class, get_class($order));

        $assignee = new TicketAssignee();
        $assignee->name = 'Pippo';
        $assignee->lastName = 'Pluto';
        $assignee->emailId = 'pippopluto@example.com';

        $buyer = new TicketBuyer();
        $buyer->name = 'Pippo';
        $buyer->lastName = 'Pluto';
        $buyer->emailId = 'pippopluto@example.com';

        foreach ($order->getTickets() as $ticket) {
            $ticket->assignTo($assignee);
        }

        $order->boughtBy($buyer);
        $order->confirm();

        $order = $portal->orders->update($order);
        $this->assertEquals(CompletedOrder::class, get_class($order));

    }
}