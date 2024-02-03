<?php

if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool
    {
        return '' === $needle || false !== strpos($haystack, $needle);
    }
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$file = file_get_contents('main1.scss');

$lines = explode(PHP_EOL, $file);

foreach ($lines as $line) {
    if (str_contains($line, '-moz-') || str_contains($line, '-webkit-') || str_contains($line, '-ms-')) {
        if( str_contains($line, '::-webkit') || str_contains($line, '@-webkit-') ) {
            echo $line.PHP_EOL;
        }
    } else {
        echo $line.PHP_EOL;
    }
} 
