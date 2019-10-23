<?php


namespace Weble\ZohoBackstageApi\Models;

/**
 * @package Weble\ZohoBackstageApi\Models
 *
 * @property string $name
 * @property string $lastName
 * @property string $emailId
 * @property string $mobile
 * @property bool   $needInvoice
 * @property string $billingAddress
 * @property string $taxRegistrationNumber
 */
class TicketBuyer extends TicketAssignee
{
    public function __construct($data = [], $baseUrl = null)
    {
        $emptyData = [
            'needInvoice' => false,
            'billingAddress' => null,
            'taxRegistrationNumber' => null
        ];

        $data = array_merge($emptyData, $data);

        parent::__construct($data, $baseUrl);
    }

    public function getName(): string
    {
        return 'currentOrder';
    }
}