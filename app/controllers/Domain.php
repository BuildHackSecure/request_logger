<?php


namespace Controller;


class Domain
{

    private static $d;

    public static function get(){
        return self::$d;
    }

    public static function set($str){
        self::$d = $str;
    }


}