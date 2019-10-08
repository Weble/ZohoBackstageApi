<?php


namespace Weble\ZohoBackstageApi\Models;


use Weble\ZohoBackstageApi\Modules\Events;
use Weble\ZohoBackstageApi\Modules\Orders;

/**
 * Class Portal
 * @package Weble\ZohoBackstageApi\Models
 *
 * @property-read Events $events
 * @property-read Orders $orders
 */
class Portal extends Model
{
    protected $availableModules = [
        'events' => Events::class,
        'orders' => Orders::class
    ];

    public function getName(): string
    {
        return 'portal';
    }
}