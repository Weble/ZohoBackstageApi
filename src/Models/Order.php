<?php


namespace Weble\ZohoBackstageApi\Models;

/**
 * Class Order
 * @package Weble\ZohoBackstageApi\Models
 *
 * @property array $attendees
 */
class Order extends Model
{
    public function __construct($data = [], $baseUrl = null)
    {
        parent::__construct($data, $baseUrl);

        if (isset($data['attendees'])) {
            foreach ($this->data['attendees'] as &$attendee) {
                $attendee = new Attendee($attendee);
            }
        }

        if (isset($data['orderTickets'])) {
            foreach ($this->data['orderTickets'] as &$ticket) {
                $ticket = new Ticket($ticket);
            }
        }
    }

    public function toArray()
    {
        $data =  parent::toArray();

        if (isset($data['attendees'])) {
            foreach ($data['attendees'] as &$attendee) {
                $attendee = $attendee->toArray();
            }
        }
        if (isset($data['orderTickets'])) {
            foreach ($data['orderTickets'] as &$ticket) {
                $ticket = $ticket->toArray();
            }
        }

        return $data;
    }
}