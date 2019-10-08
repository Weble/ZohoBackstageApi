<?php


namespace Weble\ZohoBackstageApi\Models;

class Order extends Model
{
    public function __construct($data = [], $baseUrl = null)
    {
        $emptyData = [
            'discountAmount' => 0,
            'promoCode' => '',
            'status' => 1,
            'taxAmount' => 0,
            'ticketBuyer' => null,
            'ticketCarts' => [],
            'ticketAssignee' => null,
            'ticketClass' => null,
            'ticketId' => null,
            'ticketContainer' => null,
            'totalAmount' => 0
        ];

        $data = array_merge($emptyData, $data);

        parent::__construct($data, $baseUrl);
    }

    public function withTicketClass(TicketClass $class): self
    {
        $this->ticketContainer = $class->ticketContainer()->id;
        $this->ticketCarts = [
            [
                'ticketId' => null,
                'ticketAssignee' => null,
                'ticketClass' => $class->id
            ]
        ];

        return $this;
    }

    public function boughtBy(string $email, string $firstName = '', string $lastName = '', bool $assign = true): self
    {
        $ticketBuyer = [
            'emailId' => $email,
            'lastName' => $lastName,
            'name' => $firstName
        ];

        $this->ticketBuyer = $ticketBuyer;

        if ($assign) {
            foreach ($this->ticketCarts as $ticketCart) {
                $ticketCart['ticketAssignee'] = $ticketBuyer;
            }
        }

        return $this;
    }

    public function forAssignee(string $email, string $firstName = '', string $lastName = ''): self
    {
        foreach ($this->ticketCarts as $ticketCart) {
            $ticketCart['ticketAssignee'] = [
                'emailId' => $email,
                'lastName' => $lastName,
                'name' => $firstName
            ];
        }

        return $this;
    }

    public function getName(): string
    {
        return 'currentOrder';
    }
}