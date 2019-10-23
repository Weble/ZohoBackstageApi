<?php


namespace Weble\ZohoBackstageApi\Models;

use function Sabre\Event\Loop\instance;

/**
 * @package Weble\ZohoBackstageApi\Models
 *
 * {
        * "id": "1",
        * "ticketId": 1,
        * "ticketClass": "433000000041646",
        * "ticketAssignee":
        * {
            * "id": "1",
            * "name": "Daniele",
            * "lastName": "Rosario",
            * "emailId": "daniele@weble.it"
        * }
    * }
 */
class TicketCart extends Model
{
    /**
     * @var TicketClass
     */
    public $ticketClass;
    /**
     * @var TicketAssignee
     */
    private $ticketAssignee;

    public function __construct($data = [], $baseUrl = null)
    {
        $emptyData = [
            'id' => null,
            'ticketId' => null,
            'ticketClass' => null,
            'ticketAssignee' => (new TicketAssignee())->toArray()
        ];

        $data = array_merge($emptyData, $data);

        parent::__construct($data, $baseUrl);

        if ($data['ticketClass'] instanceof TicketClass) {
            $this->ticketClass = $data['ticketClass'];
            $data['ticketClass'] = $this->ticketClass->toArray();
            return;
        }

        if (is_array($data['ticketClass'])) {
            $this->ticketClass = new TicketClass($data['ticketClass']);
        }

        if (is_string($data['ticketClass'])) {
            $this->ticketClass = new TicketClass([
                'id' => $data['ticketClass']
            ]);
        }
    }
    
    public function assignTo(TicketAssignee $assignee): self 
    {
        $this->ticketAssignee = $assignee;
        return $this;
    }

    public function toArray()
    {
        $data = parent::toArray();

        $data['ticketClass'] = $this->ticketClass->getId();
        $data['ticketAssignee'] = $this->ticketAssignee ? $this->ticketAssignee->toArray() : null;

        return $data;
    }

    public function getName(): string
    {
        return 'currentOrder';
    }
}