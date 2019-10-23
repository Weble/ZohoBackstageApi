<?php


namespace Weble\ZohoBackstageApi\Modules;

use Weble\ZohoBackstageApi\Models\Order;

class Orders extends Module
{
    const DEFAULT_TIMEZONE = 'Europe/Rome';

    /** @var string */
    private $tz = self::DEFAULT_TIMEZONE;

    public function setTimeZone($tz): self
    {
        $this->tz = $tz;
        return $this;
    }

    public function getEndpoint(): string
    {
        return 'eventOrders';
    }

    public function create(Order $order, $params = []): Order
    {
        if (!isset($params['browserTimezone'])) {
            $params['browserTimezone'] = $this->tz;
        }

        $data = $this->client->processResult(
            $this->client->call($this->getEndpoint(), 'POST', [
                'query' => $params,
                'json' => [
                    'placeOrder' => $order->toArray()
                ]
            ])
        );

        return new Order($data['order']);
    }

    public function update(Order $order, $params = []): CompletedOrder
    {
        if (!isset($params['browserTimezone'])) {
            $params['browserTimezone'] = $this->tz;
        }

        $data = $this->client->processResult(
            $this->client->call($this->getEndpoint() . '/' . $order->getId(), 'PUT', [
                'query' => $params,
                'json' => [
                    'currentOrder' => $order->toArray()
                ]
            ])
        );

        return new CompletedOrder($data);
    }

    public function getName(): string
    {
        return 'order';
    }

    protected function getResourceKey(): string
    {
        return 'currentOrder';
    }

    public function getModelClassName(): string
    {
        return Order::class;
    }
}