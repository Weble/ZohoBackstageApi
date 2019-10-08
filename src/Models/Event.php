<?php


namespace Weble\ZohoBackstageApi\Models;


use Weble\ZohoBackstageApi\Modules\Events;
use Weble\ZohoBackstageApi\Modules\Module;
use Weble\ZohoBackstageApi\Modules\Tickets;


/**
 * Class Event
 * @package Weble\ZohoBackstageApi\Models
 *
 * @property-read Tickets $tickets
 */
class Event extends Model
{
    protected $availableModules = [
        'tickets' => Tickets::class
    ];

    public function getKeyName()
    {
        return 'eventId';
    }

    public function __construct($data = [], $baseUrl = null)
    {
        foreach ($data['meta'] as $metaKey => $meta) {
            foreach ($meta as $key => $value) {
                if ($metaKey === 'event') {
                    $data[$key] = $value;
                } else {
                    if (!isset($data[$metaKey])) {
                        $data[$metaKey] = [];
                    }
                    $data[$metaKey][$value['langCode']] = $value;
                }

            }
        }

        unset($data['meta']);

        parent::__construct($data, $baseUrl);
    }

    public function createModule($name): Module
    {
        $module = parent::createModule($name);

        if ($module instanceof Tickets) {
            return $module
                ->setEventId($this->getId())
                ->setPortalId($this->portalId);
        }

        return $module;
    }

    public function getName(): string
    {
        return 'event';
    }
}