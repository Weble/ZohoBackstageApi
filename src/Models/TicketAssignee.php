<?php


namespace Weble\ZohoBackstageApi\Models;

/**
 * @package Weble\ZohoBackstageApi\Models
 *
 * @property string $name
 * @property string $lastName
 * @property string $emailId
 * @property string $mobile
 */
class TicketAssignee extends Model
{
    public function __construct($data = [], $baseUrl = null)
    {
        $emptyData = [
            'name' => null,
            'lastName' => null,
            'emailId' => null,
            'mobile' => null
        ];

        $data = array_merge($emptyData, $data);

        parent::__construct($data, $baseUrl);
    }

    public function getName(): string
    {
        return 'currentOrder';
    }
}