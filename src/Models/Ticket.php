<?php


namespace Weble\ZohoBackstageApi\Models;

use function Sabre\Event\Loop\instance;

/**
 * @package Weble\ZohoBackstageApi\Models
 *
 * {
        "id": "433000000153011",
        "ticketId": "4330000001530091",
        "ticketPrice": 0,
        "discount": 0,
        "serviceTaxedPrice": 0,
        "taxedPrice": 0,
        "totalPrice": 0,
        "ticketClass": "433000000105890",
        "eventOrder": "433000000153009",
        "emailId": "pippopluto@example.com",
        "createdBy": "433000000002001",
        "lastModifiedBy": "433000000002001",
        "createdTime": "2019-10-18T09:29:54.443Z",
        "lastModifiedTime": "2019-10-18T09:29:54.443Z"
    }
 */
class Ticket extends Model
{
    public function getName(): string
    {
        return 'ticket';
    }

}