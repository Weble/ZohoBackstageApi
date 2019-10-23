<?php


namespace Weble\ZohoBackstageApi\Models;

/**
 * When first creating an order, the required structure is
 * {
        "totalAmount": 0,
        "discountAmount": 0,
        "taxAmount": 0,
        "isTaxApplied": false,
        "promoCode": "",
        "paymentHostPageId": null,
        "checkoutNewPaymentUrl": null,
        "status": 1,
        "appRedirectUrl": null,
        "createdBy": null,
        "lastModifiedBy": null,
        "createdTime": null,
        "lastModifiedTime": null,
        "currentBrowserTime": null,
        "ticketCarts": [
        {
            "ticketId": null,
            "ticketClass": "433000000041646",
            "ticketAssignee": null
        }],
        "ticketBuyer": null,
        "ticketContainer": "433000000041643"
    }
 *
 * @package Weble\ZohoBackstageApi\Models
 *
 * @property array $ticketCarts
 * @property string|object $ticketContainer
 */
class Order extends Model
{
    /**
     * @var TicketCart[]
     */
    protected $ticketCarts = [];

    public function __construct($data = [], $baseUrl = null)
    {
        $emptyData = [
            'discountAmount' => 0,
            'promoCode' => '',
            'status' => 1,
            'taxAmount' => 0,
            'ticketBuyer' => null,
            'ticketCarts' => [],
            'ticketId' => null,
            'ticketContainer' => null,
            'totalAmount' => 0
        ];

        $data = array_merge($emptyData, $data);

        parent::__construct($data, $baseUrl);
    }

    public function setTicketContainer(TicketContainer $container): self
    {
        $this->ticketContainer = $container;
        return $this;
    }

    public function addTicket(TicketClass $class): self
    {
        $cart = new TicketCart();
        $cart->ticketClass = $class;

        $this->ticketCarts[] = $cart;

        return $this;
    }

    public function getName(): string
    {
        return 'currentOrder';
    }

    public function toArray()
    {
        $data = parent::toArray();

        $data['ticketContainer'] = $this->ticketContainer->getId();

        $data['ticketCarts'] = [];

        /** @var TicketCart $cart */
        foreach ($this->ticketCarts as $cart) {
            $data['ticketCarts'][] = $cart->toArray();
        }

        return $data;
    }
}