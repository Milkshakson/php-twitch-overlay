<?php
defined('ROOTDIR') or define('ROOTDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);
defined('APPPATH') or define('APPPATH', ROOTDIR . 'App' . DIRECTORY_SEPARATOR);
if (ENVIRONMENT != 'production')
    include_once 'displayErrors.php';
require_once('App/Core/App.php');
require_once ROOTDIR . 'vendor/autoload.php';
require_once ROOTDIR . 'App/Routes/Routes.php';