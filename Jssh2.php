<?php

/**
 * Jssh2 Operation Class
 * Jssh2 IS USED TO EXECUTE COMMAND IN REMOTE SERVERS
 *
 * @package     xxtime/Jssh2
 * @author      joe@xxtime.com
 * @link        https://github.com/thendfeel/xxtime
 * @example     http://dev.xxtime.com
 * @copyright   xxtime.com
 * @created     2014-01-11
 */
class Jssh2
{

    protected static $host = '127.0.0.1';

    protected static $port = 22;

    protected static $username = 'root';

    protected static $password = '';

    protected static $auth_key_file = '';

    protected static $auth_pubkey_file = '';

    private static $connect;

    private static function ssh_connect()
    {
        for ($i = 0; $i < 3; $i ++) {
            if (self::$connect) {
                break;
            }
            self::$connect = ssh2_connect(static::$host, static::$port);
        }
        if (static::$password) {
            ssh2_auth_password(self::$connect, static::$username, static::$password);
        } elseif (static::$auth_pubkey_file && static::$auth_key_file) {
            ssh2_auth_pubkey_file(self::$connect, 'root', static::$auth_pubkey_file, static::$auth_key_file);
        }
        return self::$connect;
    }

    public static function ssh_exec($cmd)
    {
        if (! $cmd) {
            return FALSE;
        }
        $connect = self::ssh_connect();
        $stream = ssh2_exec($connect, $cmd);
        stream_set_blocking($stream, true);
        return stream_get_contents($stream);
    }

    public static function ssh_shell($cmds = array())
    {
        if (is_string($cmds)) {
            $cmds = array(
                $cmds
            );
        } elseif (! is_array($cmds)) {
            return FALSE;
        }
        $connect = self::ssh_connect();
        $shell = ssh2_shell($connect);
        usleep(200000);
        $output = '';
        for ($i = 0; $i < count($cmds); $i ++) {
            fwrite($shell, $cmds[$i] . PHP_EOL);
            usleep(200000);
            while ($buffer = fgets($shell)) {
                flush();
                $output .= $buffer;
            }
        }
        fclose($shell);
        return $output;
    }

    public static function test()
    {
        $cmds[] = "ls -l";
        return self::ssh_shell($cmds);
    }
}


/* End of file Jmysql.php */