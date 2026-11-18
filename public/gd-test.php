<?php

echo '<pre>';

echo 'PHP Binary: ' . PHP_BINARY . PHP_EOL;
echo 'PHP SAPI: ' . PHP_SAPI . PHP_EOL;
echo 'PHP INI: ' . php_ini_loaded_file() . PHP_EOL;
echo 'Extension Dir: ' . ini_get('extension_dir') . PHP_EOL;

echo 'GD Loaded: ';
var_dump(extension_loaded('gd'));

echo 'GD Function: ';
var_dump(function_exists('imagecreatetruecolor'));

echo '</pre>';