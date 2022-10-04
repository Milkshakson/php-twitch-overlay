<?php
function pre($var, $exit = false)
{
    echo '<pre>';
    print_r($var);
    echo '</pre>';
    if ($exit)
        exit;
}
ini_set('display_errors', 1);
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
ini_set('opcache.enable', '0');
defined('ROOTDIR') or define('ROOTDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);
defined('APPPATH') or define('APPPATH', ROOTDIR . 'App' . DIRECTORY_SEPARATOR);
require_once('autoload.php');
require_once('App/Core/App.php');
require_once ROOTDIR . 'vendor/autoload.php';
require_once ROOTDIR . 'App/Routes/Routes.php';