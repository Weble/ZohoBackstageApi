<?php


namespace Weble\ZohoBackstageApi\Models;


use Weble\ZohoBackstageApi\Contracts\HasTranslations;
use Weble\ZohoBackstageApi\Mixins\HasTranslationsTrait;
use Weble\ZohoBackstageApi\Modules\Tickets;


/**
 * Class Event
 * @package Weble\ZohoBackstageApi\Models
 *
 * @property-read Tickets $tickets
 */
class Event extends Model implements HasTranslations
{
    use HasTranslationsTrait;

    protected $casts = [
        'startDate' => 'datetime',
        'endDate' => 'datetime',
        'timezone' => 'timezone'
    ];

    public function translatedFields(): array
    {
        return [
            'name',
            'description',
            'summary'
        ];
    }

    public function getName(): string
    {
        return 'event';
    }
}