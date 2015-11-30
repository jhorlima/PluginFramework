<?php
namespace Core\Service;

class Log {

    public static function register($path, $data){
        if(is_string($data))
            error_log($data.PHP_EOL, 3, "{$path}log/data.log");
        else
            error_log(var_export($data, true).PHP_EOL, 3, "{$path}log/data.log");
    }
}