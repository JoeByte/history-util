<?php

/**
 * Jkiller 自毁程序
 *
 * @package     xxtime/Jkiller.php
 * @author      joe@xxtime.com
 * @link        https://github.com/thendfeel/xxtime
 * @link        http://git.oschina.net/thendfeel/xxtime
 * @example     http://dev.xxtime.com
 * @copyright   xxtime.com
 * @created     2014-03-28
 */

// Example:
// include 'Jkiller.php';
// $killer = new Jkiller();
// $killer->kill();
class Jkiller
{

    public $key = 'abcdefg';

    public $app = '/test';

    public $removeApp = FALSE;

    public $removeDb = FALSE;

    private $host = 'localhost';

    private $port = 3306;

    private $dbname = '';

    private $user = '';

    private $password = '';

    public function __construct()
    {
        $this->app = dirname(__FILE__) . $this->app;
        $this->ready(5);
        $this->authCheck();
        $this->config();
    }

    public function kill()
    {
        if ($this->removeApp) {
            $this->removeApp($this->app);
        }
        if ($this->removeDb) {
            $this->removeDb();
        }
    }

    private function removeApp($dir = '')
    {
        if (is_dir($dir)) {
            $dir_list = scandir($dir);
            unset($dir_list[0], $dir_list[1]);
            foreach ($dir_list as $value) {
                $item = $dir . '/' . $value;
                if (is_dir($item)) {
                    $this->removeApp($item);
                } else {
                    unlink($item);
                    echo $item . "\r\n";
                }
            }
            rmdir($dir);
            echo $dir . "\r\n";
        }
    }

    private function removeDb()
    {
        ;
    }

    private function authCheck()
    {
        $string = '';
        if (isset($_GET)) {
            $sign = isset($_GET['sign']) ? $_GET['sign'] : '';
            unset($_GET['sign']);
            ksort($_GET);
            foreach ($_GET as $key => $value) {
                $string .= "{$key}{$value}";
            }
        }
        $code = md5(date('Y-m-d') . $this->key . $string);
        $time = isset($_GET['time']) ? $_GET['time'] : 0;
        if (abs($time - time()) > 600) {
            exit('授权未通过');
        }
        if ($code != $sign) {
            exit('授权未通过');
        }
    }

    private function config()
    {
        $removeApp = isset($_GET['removeApp']) ? $_GET['removeApp'] : FALSE;
        $removeDb = isset($_GET['removeDb']) ? $_GET['removeDb'] : FALSE;
        if ($removeApp) {
            $this->removeApp = TRUE;
        }
        if ($removeDb) {
            $this->removeDb = TRUE;
        }
    }

    private function ready($second)
    {
        echo "\r\n\r\n<pre>";
        echo "--------------------自毁程序启动--------------------\r\n";
        echo "摧毁目标: $this->app \r\n\r\n";
        echo "$second 秒后执行删除操作\r\n\r\n";
        echo "----------------------------------------------------\r\n";
        for ($i = $second; $i > 0; $i --) {
            sleep(1);
            echo "$i 秒\r\n";
        }
    }
}

/* End of file Jkiller.php */