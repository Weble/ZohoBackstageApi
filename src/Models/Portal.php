<?php


namespace Weble\ZohoBackstageApi\Models;


use Weble\ZohoBackstageApi\Modules\Events;
use Weble\ZohoBackstageApi\Modules\Orders;
use Weble\ZohoBackstageApi\Modules\Venues;

/**
 * Class Portal
 * @package Weble\ZohoBackstageApi\Models
 *
 * @property-read Events $events
 * @property-read Orders $orders
 * @property-read Venues $venues
 */
class Portal extends Model
{
    protected $availableModules = [
        'events' => Events::class,
        'venues' => Venues::class,
        'orders' => Orders::class
    ];

    public function getName(): string
    {
        return 'portal';
    }
}