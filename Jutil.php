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
 * 写日志文件
 * Write File
 */
function write_file($text = '', $fileName = 'log.txt')
{
    $fileName = dirname(__FILE__) . '/' . $fileName;
    $handle = fopen($fileName, "a+b");
    $text .= "\r\n";
    fwrite($handle, $text);
    fclose($handle);
}

/**
 * 创建随机字符串
 * Create Random String
 */
function gen_random($length = 8)
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
    ;
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
    ;
    echo '</pre>';
    exit('---------- Debug End ----------');
}


/* End of file Jutil.php */
