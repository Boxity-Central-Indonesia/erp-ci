<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth
{
    private static $secret_key_pin = 'AfindoInf.jbg123';

    public static function enc($data)
    {
        return openssl_encrypt($data, 'aes128', self::$secret_key_pin, false, self::$secret_key_pin);
    }

    public static function dec($data)
    {
        return openssl_decrypt($data, 'aes128', self::$secret_key_pin, false, self::$secret_key_pin);
    }
} 
