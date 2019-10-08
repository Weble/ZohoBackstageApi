<?php


namespace Weble\ZohoBackstageApi\Modules;


use Tightenco\Collect\Support\Collection;
use Weble\ZohoBackstageApi\Client;
use Weble\ZohoBackstageApi\Models\Event;
use Weble\ZohoBackstageApi\Models\TicketClass;
use Weble\ZohoBackstageApi\Models\TicketContainer;

class Tickets extends Module
{
    /**
     * @var string
     */
    private $portalId;
    /**
     * @var string
     */
    private $eventId;

    /** @var Collection */
    private $details;

    public function setPortalId(string $portalId): self
    {
        $this->portalId = $portalId;
        return $this;
    }

    public function setEventId(string $eventId): self
    {
        $this->eventId = $eventId;
        return $this;
    }

    public function getEndpoint(): string
    {
        return Client::ROOT_URI.'tickets';
    }

    public function ticketContainers(): Collection
    {
        return
            collect(
                $this
                    ->details()
                    ->get('ticketContainers', [])
            )->mapWithKeys(function ($data) {
                return [$data['id'] => new TicketContainer($data)];
            });
    }

    public function ticketClasses(): Collection
    {
        return
            collect(
                $this
                    ->details()
                    ->get('ticketClasses', [])
            )->map(function ($data) {
                $translation = $this->ticketClassTranslations()->where('ticketClass', $data['id'])->first();
                $data['ticketContainer'] = $this->ticketContainers()->get($data['ticketContainer'])->toArray();
                $data['name'] = $translation['name'];
                $data['description'] = $translation['description'];
                return new TicketClass($data);
            });
    }

    public function metaDetails(): Collection
    {
        return collect($this->details()->get('eventTicketMetaDetails', []));
    }

    public function available(): bool
    {
        return $this->metaDetails()->get('isTicketsAvailable', false);
    }

    public function ticketClassTranslations(): Collection
    {
        return
            collect(
                $this
                    ->details()
                    ->get('ticketClassTranslations', [])
            );
    }

    public function details($eventId = null, $portalId = null): Collection
    {
        if ($this->details) {
            return $this->details;
        }

        if ($portalId) {
            $this->setPortalId($portalId);
        }

        if ($eventId) {
            $this->setEventId($eventId);
        }

        $this->details = collect($this->getClient()->get($this->getEndpoint(), $this->eventId, [
            'portalId' => $this->portalId
        ]));

        return $this->details;
    }

    public function getName(): string
    {
        return 'tickets';
    }

    protected function getResourceKey(): string
    {
        return 'completedEventsMetas';
    }

    public function getModelClassName(): string
    {
        return Event::class;
    }
}