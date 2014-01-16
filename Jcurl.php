<?php

/**
 * Jcurl Operation Class
 * Jcurl IS USED TO REQUEST URL OR POST DATAS
 *
 * @package Uacool/Jcurl
 * @author thendfeel@gmail.com
 * @link https://github.com/thendfeel/Uacool
 * @example http://dev.uacool.com
 * @copyright uacool.com
 * @created 2014-01-16
 */

// Use Exp
// Jcurl::$cookie = 'uid=229165395; _is_admin=1;';
// $output = Jcurl::get('http://www.uacool.com/229165395');
// Jcurl::writeFile($output);
class Jcurl
{

    public static $url = '';

    public static $cookie = '';

    public static $cookie_file = '';

    public static $postData = array();

    private static $curl;

    private static $output;

    /**
     * Init The Curl
     *
     * @return resource
     */
    private static function init()
    {
        if (self::$curl) {
            return self::$curl;
        }
        self::$curl = curl_init();
        $option = array(
            CURLOPT_URL => self::$url,
            CURLOPT_HEADER => FALSE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_AUTOREFERER => TRUE,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1; WOW64) Apple WebKit/537.36 (KHTML, like Gecko) Chrome/30.0.1599.101 Safari/537.36',
            CURLOPT_COOKIE => self::$cookie,
            CURLOPT_COOKIEFILE => self::$cookie_file,
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_FOLLOWLOCATION => TRUE,
            CURLOPT_MAXREDIRS => 10
        );
        curl_setopt_array(self::$curl, $option);
        return self::$curl;
    }

    /**
     * Exec Curl And Return The Result
     *
     * @return mixed
     */
    private static function go()
    {
        self::$output = curl_exec(self::$curl);
        curl_close(self::$curl);
        return self::$output;
    }

    /**
     * Write The Contents To A File
     *
     * @param string $text            
     * @param string $fileName            
     */
    public static function writeFile($text = '', $fileName = 'Jcurl.html')
    {
        $fileName = dirname(__FILE__) . '/' . $fileName;
        $handle = fopen($fileName, "a+b");
        $text .= "\r\n";
        fwrite($handle, $text);
        fclose($handle);
    }

    /**
     * Get The Content With A URL
     *
     * @param string $url            
     */
    public static function get($url = NULL)
    {
        if (! empty($url)) {
            self::$url = $url;
        }
        self::init();
        return self::go();
    }

    /**
     * Post Data To URL
     *
     * @param string $url            
     * @param array $data            
     */
    public static function post($url = NULL, $data = array())
    {
        if (! empty($url)) {
            self::$url = $url;
        }
        if (! empty($data)) {
            self::$postData = $data;
        }
        self::init();
        curl_setopt(self::$curl, CURLOPT_POST, TRUE);
        curl_setopt(self::$curl, CURLOPT_POSTFIELDS, self::$postData);
        return self::go();
    }

    /**
     * Save Cookies And Ensure The Path Could Be Write
     *
     * @param string $path            
     */
    public static function saveCookie($path = '/home/_JcurlCookie')
    {
        self::init();
        curl_setopt(self::$curl, CURLOPT_COOKIEJAR, $path);
    }
}