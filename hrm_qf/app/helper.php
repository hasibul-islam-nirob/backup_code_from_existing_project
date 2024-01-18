<?php
use Symfony\Component\VarDumper\VarDumper;

if (!function_exists('ss')) {

    function ss(...$vars)
    {
        http_response_code(500);
        foreach ($vars as $v) {
            VarDumper::dump($v);
        }

        exit(1);
    }
    
}