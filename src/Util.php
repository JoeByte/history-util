<?php

namespace Xxtime;

class Util
{

    function debug()
    {
        echo "<pre>\r\n";
        array_map(function ($x) {
            print_r($x);
            echo "\r\n<br />\r\n";
        }, func_get_args());
        die;
    }

    /**
     * var_dump For Html Page Debug Output
     * 调试函数
     */
    function dbv()
    {
        echo '<pre>';
        if (func_num_args()) {
            foreach (func_get_args() as $k => $v) {
                echo "------- dbv $k -------<br/>";
                var_dump($v);
                echo "<br/>";
            }
        }
        echo '</pre>';
        exit('---------- Debug End ----------');
    }

    /**
     * print_r For Html Page Debug Output
     * 调试函数
     */
    function dbc()
    {
        echo '<pre>';
        if (func_num_args()) {
            foreach (func_get_args() as $k => $v) {
                echo "------- dbx $k -------<br/>";
                print_r($v);
                echo "<br/>";
            }
        }
        echo '</pre>';
    }

    /**
     * 创建随机字符串
     * Create Random String
     */
    function random($length = 8)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            // $string .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
            $string .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $string;
    }

    /**
     * 获取参数
     *
     * @param string $param
     * @param string $default
     * @return Ambigous <mixed, string>|Ambigous <multitype:, mixed>
     */
    function param($param = '', $default = '')
    {
        $search = array(
            '\\',
            '/',
            '"',
            "'",
            ';'
        );
        if ($param) {
            return isset($_POST[$param]) ? str_replace($search, '', $_POST[$param]) : (isset($_GET[$param]) ? str_replace($search, '', $_GET[$param]) : $default);
        }
        $param = array();
        if ($_GET) {
            foreach ($_GET as $key => $value) {
                $param[$key] = str_replace($search, '', $value);
            }
        }
        if ($_POST) {
            foreach ($_POST as $key => $value) {
                $param[$key] = str_replace($search, '', $value);
            }
        }
        return $param;
    }

    /**
     * 创建签名
     *
     * @param array $array
     * @param string $signKey
     * @return string
     */
    function create_sign($array, $signKey = '')
    {
        ksort($array);
        $string = '';
        foreach ($array as $key => $value) {
            $string .= "$key=$value&";
        }
        return md5(rtrim($string, "&") . $signKey);
    }

    /**
     * 写日志文件
     * Write File
     *
     * @param string $text
     * @param string $file
     * @param string $append
     * @return boolean
     */
    function write_file($text = '', $file = 'log.txt', $append = TRUE)
    {
        $text = var_export($text, TRUE);
        if (strpos($file, '/') === 0) {
            //return FALSE;
        } else {
            $file = dirname(__FILE__) . '/' . $file;
        }
        if ($append) {
            $handle = fopen($file, "a+b");
        } else {
            $handle = fopen($file, "w+b");
        }
        $text .= "\r\n";
        fwrite($handle, $text);
        fclose($handle);
    }

    /**
     * @use write_log('abcd', '/data/logs.txt');
     * @use write_log('abcd', '../logs.txt');
     * @param string $text
     * @param string $file
     * @return bool
     */
    function write_log($text = '', $file = 'log.log')
    {
        if (strpos($file, '/') === 0) {
            //return FALSE;
        } else {
            $file = dirname(__FILE__) . '/' . $file;
        }
        $handle = fopen($file, "a+b");
        $text = date('Y-m-d H:i:s') . ' ' . $text . "\r\n";
        fwrite($handle, $text);
        fclose($handle);
    }

    public function output($data = array())
    {
        exit(json_encode($data));
    }

}
