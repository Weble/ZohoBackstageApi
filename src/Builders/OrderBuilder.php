<?php


namespace Weble\ZohoBackstageApi\Builders;


use Weble\ZohoBackstageApi\Models\Event;
use Weble\ZohoBackstageApi\Models\Model;

class OrderBuilder extends Model
{
    public function __construct($data = [], $baseUrl = null)
    {
        parent::__construct($data, $baseUrl);

        $this->data['quantity'] = 1;
    }

    public function assignTo(string $email): self
    {
        $this->data['assignees'] = $email;
        return $this;
    }

    public function forEvent($event): self
    {
        if ($event instanceof Event) {
            $event = $event->getId();
        }
        $this->data['eventId'] = $event;
        return $this;
    }

    public function boughtBy(string $email): self
    {
        $this->data['ticketBuyer'] = $email;
        return $this;
    }

    public function quantity(int $qty): self
    {
        $this->data['quantity'] = $qty;
        return $this;
    }
}