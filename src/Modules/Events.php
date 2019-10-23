<?php


namespace Weble\ZohoBackstageApi\Modules;


use Tightenco\Collect\Support\Collection;
use Weble\ZohoBackstageApi\Mixins\WithTranslationsData;
use Weble\ZohoBackstageApi\Models\Event;
use Weble\ZohoBackstageApi\Models\Venue;

class Events extends Module
{
    use WithTranslationsData;

    public function getEndpoint(): string
    {
        return 'events';
    }

    public function getList($params = [])
    {
        $list = $this->oAuthClient->getList($this->baseUrl.$this->getEndpoint());
        $events = $this->createCollectionFromList($list, 'events');

        $venues = $this->extractVenuesFromList($list);

        $eventLanguages = $this->extractLanguagesFrom($list, 'eventLanguages');
        $defaultLanguage = $this->extractDefaultLanguageFrom($list, 'eventLanguages');

        $eventTranslations = (new Collection($list['eventTranslations']))->keyBy('eventLanguage')->toArray();

        $events = $events->map(function (Event $event) use ($venues, $defaultLanguage, $eventLanguages, $eventTranslations) {
            $event->setAvailableLanguages($eventLanguages);
            $event->setDefaultLanguage($defaultLanguage);
            $event->setTranslationsFrom($eventTranslations);

            $event->venue = $venues->get($event->getId());

            return $event;
        });

        return $events;
    }

    public function venues()
    {
        $list = $this->oAuthClient->getList($this->baseUrl.$this->getEndpoint());
        return $this->extractVenuesFromList($list)->keyBy('id');
    }

    public function extractVenuesFromList($list): Collection
    {
        $venues = $this->createCollectionFromList($list, 'venues', Venue::class);

        $eventLanguages = $this->extractLanguagesFrom($list, 'eventLanguages');
        $defaultLanguage = $this->extractDefaultLanguageFrom($list, 'eventLanguages');

        $venueTranslations = (new Collection($list['venueTranslations']))->keyBy('eventLanguage')->toArray();

        $venues = $venues->mapWithKeys(function (Venue $venue) use ($defaultLanguage, $eventLanguages, $venueTranslations) {
            $venue->setAvailableLanguages($eventLanguages);
            $venue->setDefaultLanguage($defaultLanguage);
            $venue->setTranslationsFrom($venueTranslations);

            return [$venue->event => $venue];
        });

        return $venues;
    }

    public function get($id, array $params = [])
    {
        $list = self::getList($params);

        return $list->get($id);
    }

    public function getName(): string
    {
        return 'events';
    }

    protected function getResourceKey(): string
    {
        return null;
    }

    public function getModelClassName(): string
    {
        return Event::class;
    }

}