<?php


namespace Weble\ZohoBackstageApi\Models;


use Weble\ZohoBackstageApi\Modules\Events;


class TicketClass extends Model
{
    public function getName(): string
    {
        return 'ticketClass';
    }

    public function ticketContainer(): TicketContainer
    {
        return new TicketContainer($this->ticketContainer);
    }

}