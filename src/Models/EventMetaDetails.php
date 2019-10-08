<?php


namespace Weble\ZohoBackstageApi\Models;


use Tightenco\Collect\Support\Collection;

/**
 * Class EventMetaDetails
 * @package Weble\ZohoBackstageApi\Models
 *
 * @property-read int $completedCount
 * @property-read bool $hasOnlineEvent
 * @property-read int $liveCount
 * @property-read int $runningEventsCount
 */
class EventMetaDetails extends Model
{
    public function getName(): string
    {
        return 'eventsMetaDetails';
    }

    public function cities(): Collection
    {
        return collect($this->cities)->filter();
    }
}