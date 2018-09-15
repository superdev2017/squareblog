<?php

namespace PropelConversions;

/**
 * Created by PhpStorm.
 * User: ivanristic
 * Date: 1/5/17
 * Time: 1:04 PM
 */
class PropelConversions
{
    private $domain;
    private $api_key;

    private static $routes = [
        'get_customer'              => '/api/s/v1/customer/{id}.json',
        'update_customer'           => '/api/s/v1/subscription/customer/update.json',
        'get_subscription'          => '/api/s/v1/subscription/{id}.json',
        'cancel_subscription'       => '/api/s/v1/subscription/cancel.json',
        'get_orders'                => '/api/s/v1/customer/{id}/orders',
        'get_invoice'               => '/api/s/v1/order/{id}/invoice'
    ];

    public function __construct($domain, $api_key)
    {
        $this->domain = $domain;
        $this->api_key = $api_key;
    }

    public function getSubscription($id)
    {
        $data = $this->get('get_subscription', [
            'id' => $id
        ]);

        if (is_object($data) && property_exists($data, 'data')) {
            return $data->data;
        }

        return null;
    }

    public function getCustomer($id)
    {
        $data = $this->get('get_customer', [
            'id' => $id
        ]);

        if (is_object($data) && property_exists($data, 'data')) {
            return $data->data;
        }

        return null;
    }

    public function updateCustomer($data) {
        return $this->post('update_customer', $data);
    }

    public function cancelSubscription($subscription_id, $email)
    {
        $data = $this->post('cancel_subscription', [
            'id'    => $subscription_id,
            'email' => $email
        ]);

        return $data;
    }

    public function getOrders($user_id)
    {
        $data = $this->get('get_orders', [
            'id'    => $user_id
        ]);

        if (is_object($data) && property_exists($data, 'data')) {
            return $data->data;
        }

        return null;
    }

    public function getInvoice($order_id)
    {
        $data = $this->get('get_invoice', [
            'id'    => $order_id
        ]);

        if (is_object($data) && property_exists($data, 'data')) {
            return $data->data;
        }

        return null;
    }

    private function post($route, $data) {

        $endpoint = $this->domain . self::$routes[$route] . '?apikey='.$this->api_key;

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response);

        return $data;
    }

    private function get($route, $data) {

        $endpoint = $this->domain . self::$routes[$route] . '?apikey='.$this->api_key;

        foreach ($data as $key => $value) {
            if (strpos($endpoint, '{'.$key.'}') !== false) {
                $endpoint = str_replace('{'.$key.'}', $value, $endpoint);
            } else {
                $endpoint .= '&' . $key . '=' . $value;
            }
        }

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response);

        return $data;
    }
}