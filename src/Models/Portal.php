<?php


namespace Weble\ZohoBackstageApi\Models;


use Weble\ZohoBackstageApi\Modules\Events;

/**
 * Class Portal
 * @package Weble\ZohoBackstageApi\Models
 *
 * @property-read Events $events
 */
class Portal extends Model
{
    protected $availableModules = [
        'events' => Events::class
    ];

    public function getName(): string
    {
        return 'portal';
    }
}