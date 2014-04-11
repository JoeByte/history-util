<?php

/**
 * Jcurl Operation Class
 * Jcurl IS USED TO REQUEST URL OR POST DATAS
 *
 * @package     xxtime/Jcurl.php
 * @version     2.0
 * @author      joe@xxtime.com
 * @link        https://github.com/thendfeel/xxtime
 * @example     http://dev.xxtime.com
 * @copyright   xxtime.com
 * @created     2014-01-16
 */

// Use Exp No.1
// $jcurl = new Jcurl();
// $jcurl->cookie = 'anonymid=hqhua3nr-jbl05x; _r01_=1';
// $output = $jcurl->get('http://blog.xxtime.com');
// print_r($output);

// Use Exp No.2
// $jcurl = new Jcurl();
// $jcurl->debug = TRUE;
// $output = $jcurl->post('http://www.xxtime.com/test.php', array('name' => 'Joe', 'site' => 'www.xxtime.com'));
class Jcurl
{

    public $url = '';

    public $cookie = '';

    public $cookie_file = '';

    public $postdata = array();

    public $timeout = 30;

    public $connecttimeout = 30;

    public $ssl_verifypeer = FALSE;

    public $ssl_verifyhost = 0;

    public $header = FALSE;

    public $encoding = '';

    public $useragent = 'Mozilla/5.0 (Windows NT 6.1; WOW64) Apple WebKit/537.36 (KHTML, like Gecko) Chrome/30.0.1599.101 Safari/537.36';

    public $format = 'json';

    public $decode_json = FALSE;

    public $http_code;

    public $http_info;

    public $debug = FALSE;

    /**
     * GET请求
     *
     * @param string $url            
     */
    public function get($url = NULL)
    {
        return $this->http($url, 'GET');
    }

    /**
     * Post请求
     *
     * @param string $url            
     * @param array $data            
     */
    public function post($url = NULL, $data = array())
    {
        if (! empty($url)) {
            $this->url = $url;
        }
        if (! empty($data)) {
            $this->post_data = $data;
        }
        return $this->http($url, 'POST', $data);
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
        curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, 2);
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
        
        if ($this->cookie) {
            curl_setopt($ci, CURLOPT_COOKIE, $this->cookie);
        }
        if ($this->cookie_file) {
            curl_setopt($ci, CURLOPT_COOKIEFILE, $this->cookie_file);
            if (! file_exists($this->cookie_file)) {
                curl_setopt($ci, CURLOPT_COOKIEJAR, $this->cookie_file);
            }
        }
        
        if ($this->header) {
            // 此处按需求自行改写
            $headers = $this->header;
        }
        
        curl_setopt($ci, CURLOPT_URL, $url);
        curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE);
        
        $response = curl_exec($ci);
        $this->http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
        $this->http_info = array_merge($this->http_info, curl_getinfo($ci));
        $this->url = $url;
        if ($this->debug) {
            echo "<pre>---------- Post Data ----------\r\n";
            var_dump($postfields);
            
            echo "---------- Headers ----------\r\n";
            print_r($headers);
            
            echo '---------- Request Info ----------' . "\r\n";
            print_r(curl_getinfo($ci));
            
            echo '---------- Response ----------' . "\r\n";
            print_r($response);
        }
        curl_close($ci);
        if ($this->decode_json) {
            $response = json_decode($response, TRUE);
        }
        return $response;
    }
}

/* End of file Jcurl.php */