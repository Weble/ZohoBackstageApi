<?php


namespace Weble\ZohoBackstageApi\Modules;

use Doctrine\Common\Inflector\Inflector;
use Weble\ZohoBackstageApi\Builders\OrderBuilder;
use Weble\ZohoBackstageApi\Exceptions\ErrorResponseException;
use Weble\ZohoBackstageApi\Models\Order;
use Weble\ZohoBackstageApi\OAuthClient;

class Orders extends Module
{
    const DEFAULT_TIMEZONE = 'Europe/Rome';

    /** @var string */
    private $tz = self::DEFAULT_TIMEZONE;


    public function getEndpoint(): string
    {
        return 'eventOrders';
    }

    public function create(OrderBuilder $order, $params = []): Order
    {
        $url = $this->baseUrl.Inflector::singularize($this->getEndpoint());

        $data = $this
            ->getClient()
            ->setApiVersion(OAuthClient::API_V0)
            ->rawPost($url, [
                'query' => $params,
                'json' => [
                    'placeOrder' => $order->toArray()
                ]
            ]);

        $this->getClient()->setApiVersion(OAuthClient::API_V1);

        if (isset($data['message'])) {
            throw new ErrorResponseException('Response from Zoho is not success. Message: '.$data['message']);
        }

        if (isset($data['errors'])) {
            throw new ErrorResponseException('Response from Zoho is not success. Message: '.json_encode($data['errors']));
        }

        return new Order($data['order']);
    }

    public function get($id, array $params = []): Order
    {
        $item = $this->oAuthClient->get($this->baseUrl.$this->getEndpoint(), $id, $params);

        $data = $item[$this->getResourceKey()];

        $data['orderTickets'] = $item['orderTickets'];
        $data['orderCost'] = $item['orderCost'];

        return $this->make($data);
    }


    public function getName(): string
    {
        return 'order';
    }

    protected function getResourceKey(): string
    {
        return 'eventOrder';
    }

    public function getModelClassName(): string
    {
        return Order::class;
    }
}