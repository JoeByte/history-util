<?php

/**
 * path是iphone上很火的一款社交app
 * sdk.path.php 则是path的sdk
 *
 * @package     xxtime/sdk.path.php
 * @author      joe@xxtime.com
 * @link        https://github.com/thendfeel/xxtime
 * @link        https://path.com/developers/docs
 * @example     http://dev.xxtime.com
 * @copyright   xxtime.com
 * @created     2014-04-07
 */
class Sdk_path
{

    public $client_id;

    public $client_secret;

    public $access_token;

    public $refresh_token;

    public $http_code;

    public $url;

    public $host = "https://partner.path.com";

    public $timeout = 30;

    public $connecttimeout = 30;

    public $ssl_verifypeer = FALSE;

    public $format = 'json';

    public $decode_json = TRUE;

    public $http_info;

    public $useragent = 'xxtime sdk for path v1.0';

    public $debug = FALSE;

    public static $boundary = '';

    public function login()
    {
        if (! isset($_GET['code'])) {
            $this->url = $this->host . '/oauth2/authenticate';
            $query_data = array(
                'response_type' => 'code',
                'client_id' => $this->client_id
            );
            $url = $this->host . '/oauth2/authenticate?' . http_build_query($query_data);
            header('Location: ' . $url);
            exit();
        } else {
            $query_data = array(
                'grant_type' => 'authorization_code',
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
                'code' => $_GET['code']
            );
            $url = $this->host . "/oauth2/access_token";
            return $this->http($url, 'POST', $query_data);
        }
    }

    /**
     * 获取用户信息
     *
     * @param string $uid            
     */
    public function getUser($uid = '')
    {
        $url = $this->host . "/1/user/{$uid}";
        return $this->http($url, 'GET');
    }

    /**
     * 获取用户好友
     *
     * @param string $uid            
     */
    public function getFriends($uid = '')
    {
        $url = $this->host . "/1/user/{$uid}/friends";
        return $this->http($url, 'GET');
    }

    public function createPhoto($msg = '', $links = '', $private = FALSE)
    {
        $time = time();
        $query_data = array(
            'timezone' => '',
            'created' => $time,
            'private' => $private,
            'captured' => $time,
            'filter' => '',
            'caption' => $msg,
            'tags' => '',
            'links' => $links
        );
        $url = $this->host . "/1/moment/photo";
        $response = $this->http($url, 'POST', $query_data);
        print_r($response);
    }

    private function http($url, $method, $postfields = NULL, $headers = array())
    {
        $this->http_info = array();
        $ci = curl_init();
        curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ci, CURLOPT_USERAGENT, $this->useragent);
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);
        curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ci, CURLOPT_ENCODING, "");
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer);
        curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, 1);
        curl_setopt($ci, CURLOPT_HEADER, FALSE);
        // curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'getHeader')); //回调
        
        switch ($method) {
            case 'POST':
                curl_setopt($ci, CURLOPT_POST, TRUE);
                if (! empty($postfields)) {
                    curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
                    $this->postdata = $postfields;
                }
                break;
            case 'GET':
                if (! empty($postfields)) {
                    $url = "{$url}?" . http_build_query($postfields);
                }
                break;
            case 'DELETE':
                curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
                if (! empty($postfields)) {
                    $url = "{$url}?{$postfields}";
                }
        }
        
        if (isset($this->access_token) && $this->access_token) {
            $headers = "Authorization: Bearer $this->access_token";
        }
        
        curl_setopt($ci, CURLOPT_URL, $url);
        curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE);
        
        $response = curl_exec($ci);
        $this->http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
        $this->http_info = array_merge($this->http_info, curl_getinfo($ci));
        $this->url = $url;
        if ($this->debug) {
            echo "<pre>---------- post data ----------\r\n";
            var_dump($postfields);
            
            echo "---------- headers ----------\r\n";
            print_r($headers);
            
            echo '---------- request info ----------' . "\r\n";
            print_r(curl_getinfo($ci));
            
            echo '---------- response ----------' . "\r\n";
            print_r($response);
        }
        curl_close($ci);
        if ($this->decode_json) {
            $response = json_decode($response, TRUE);
        }
        return $response;
    }
}


/* End of file sdk.path.php */
