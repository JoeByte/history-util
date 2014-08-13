<?php

/**
 * Jutil Operation Function
 * Util 常用函数
 *
 * @package     xxtime/Jutil.php
 * @author      joe@xxtime.com
 * @link        https://github.com/thendfeel/xxtime
 * @example     http://dev.xxtime.com
 * @copyright   xxtime.com
 * @created     2014-03-25
 */

/**
 * print_r For Html Page Debug Output
 * 调试函数
 */
function dbx()
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
    exit('---------- Debug End ----------');
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
    for ($i = 0; $i < $length; $i ++) {
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
 * @param string $key            
 * @return string
 */
function create_sign($array, $signkey = '')
{
    ksort($array);
    $string = '';
    foreach ($array as $key => $value) {
        $string .= "$key=$value&";
    }
    return md5(rtrim($string, "&") . $signkey);
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
    if (strpos($file, '/') === FALSE) {
        $file = dirname(__FILE__) . '/' . $file;
    }
    if (strpos($file, '/') !== 0) {
        return FALSE;
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
 * 成功返回
 *
 * @param string $msg            
 * @param string $code            
 */
function success($msg, $code = '200')
{
    $error = array(
        'error' => 0,
        'code' => $code,
        'msg' => $msg
    );
    exit(json_encode($error));
}

/**
 * 错误输出
 *
 * @param string $msg            
 * @param string $code            
 */
function failure($msg, $code = '202.1')
{
    $error = array(
        'error' => 1,
        'code' => $code,
        'msg' => $msg
    );
    exit(json_encode($error));
}

/* End of file Jutil.php */
