<?php

namespace IMCreator;


class IMCreator
{
    private $domain = 'http://editor.directcompositor.com';

    private $label;
    private $api_key;

    private static $routes = [
        'list_themes'              => '/api/v1/themes/{amount}',
        'publish_site'             => '/api/v1/users/{nickname}/sites/{license_id}/publish',
        'create_site'              => '/api/v1/users/{nickname}/sites',
        'create_user'              => '/api/v1/users',
        'create_license'           => '/api/v1/users/{nickname}/licenses',
        'get_user'                 => '/api/v1/users/{nickname}',
        'login_user'               => '/api/auto_login_credentials',
    ];

    public function __construct($label, $api_key)
    {
        $this->label = $label;
        $this->api_key = $api_key;
    }

    public function createUser($nickname, $email, $password) {
        $response = $this->post(self::$routes['create_user'], [
            'nickname'      => $nickname,
            'email'         => $email,
            'password'      => $password,
            'send_email'    => false
        ]);

        if ($response && $response->CODE == 200) {
            return 200;
        }

        if ($response && $response->CODE == 201) {
            return 201;
        }

        return false;
    }

    public function createSite($nickname, $theme_id) {

        $response = $this->post(
            $this->wrapParameters(
                self::$routes['create_site'],
                [
                    'nickname' => $nickname
                ]
            ),
            [
                'theme_id'  => $theme_id,
                'site_name' => $nickname
            ]
        );

        return $response;
    }

    public function publishSite($nickname, $license_id) {
        $response = $this->get(
            self::$routes['publish_site'],
            [
                'nickname'   => $nickname,
                'license_id' => $license_id
            ]
        );

        return $response;
    }

    public function createLicense($nickname, $theme_id, $siteId, $siteName, $offerName, $subscriptionId) {

        $response = $this->post(
            $this->wrapParameters(
                self::$routes['create_license'],
                [
                    'nickname' => $nickname
                ]
            ),
            [
                'nickname'          => $nickname,
                'theme_id'          => $theme_id,
                'vbid'              => $siteId,
                'site_name'         => $siteName,
                'offer_name'        => $offerName,
                'subscription_id'   => $nickname,
                'connect_domain'    => true
            ]
        );

        return $response;

    }

    public function listThemes($amount) {

        return $this->get(self::$routes['list_themes'], [
            'amount'  => $amount,
        ]);
    }

    public function authenticateUser($nickname, $password) {

        return $this->get(self::$routes['login_user'], [
            'nickname'  => $nickname,
            'password'  => md5('H7x6' . $password),
        ]);
    }

    public function wrapParameters($endpoint, $data) {

        foreach ($data as $key => $value) {
            if (strpos($endpoint, '{'.$key.'}') !== false) {
                $endpoint = str_replace('{'.$key.'}', $value, $endpoint);
            } else {
                $endpoint .= '&' . $key . '=' . $value;
            }
        }
        return $endpoint;
    }

    private function post($endpoint, $data = []) {

        $endpoint = $this->domain . $endpoint;

        $data['label']  = $this->label;
        $data['api_token'] = $this->api_key;

        /*
        echo "Request: " . $endpoint  . "<br>";
        echo "<pre>" . print_r($data) . "</pre>";
        echo "<br><br><br>";
        */

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response);

        /*
        echo "Response: ";
        echo "<pre>" . print_r($data) . "</pre>";
        echo "<br><br><br>";
        */

        return $data;
    }

    private function get($route, $data) {

        $data['label']  = $this->label;
        $data['api_token'] = $this->api_key;


        $endpoint = $this->domain . $route;

        $i = 0;
        foreach ($data as $key => $value) {
            if (strpos($endpoint, '{'.$key.'}') !== false) {
                $endpoint = str_replace('{'.$key.'}', $value, $endpoint);
            } else {
                $prepend = '&';
                if ($i == 0) {
                    $prepend = '?';
                }
                $endpoint .= $prepend . $key . '=' . $value;
                $i++;
            }
        }

        if ($route == self::$routes['login_user']) {
            return $endpoint;
        }
        /*
        echo "Request: " . $endpoint . "<br>";
        echo "<pre>" . print_r($data) . "</pre>";
        echo "<br><br><br>";
        */

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response);

        /*
        echo "Response: ";
        echo "<pre>" . print_r($data) . "</pre>";
        echo "<br><br><br>";
        */
        return $data;
    }
}