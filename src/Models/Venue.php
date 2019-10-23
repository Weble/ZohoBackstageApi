<?php


namespace Weble\ZohoBackstageApi\Models;


use Weble\ZohoBackstageApi\Contracts\HasTranslations;
use Weble\ZohoBackstageApi\Mixins\HasTranslationsTrait;
use Weble\ZohoBackstageApi\Modules\Tickets;


/**
 * Class Venue
 * @package Weble\ZohoBackstageApi\Models
 *
 * @property-read Tickets $tickets
 */
class Venue extends Model implements HasTranslations
{
    use HasTranslationsTrait;

    public function translatedFields(): array
    {
        return [
            'name',
            'steet',
            'townOrCity',
            'state',
            'countryName',
        ];
    }

    public function getName(): string
    {
        return 'venue';
    }
}