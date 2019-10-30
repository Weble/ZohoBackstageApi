<?php


namespace Weble\ZohoBackstageApi\Models;

use function Sabre\Event\Loop\instance;

class Ticket extends Model
{
    public function getName(): string
    {
        return 'ticket';
    }

}