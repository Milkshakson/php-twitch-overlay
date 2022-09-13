<?php

use App\Libraries\Dotenv;

require_once ROOTDIR . 'autoload.php';
include_once(ROOTDIR . 'App/Helpers/CommonHelper.php');
$env = new Dotenv();
defined('ENVIRONMENT') or define('ENVIRONMENT', $env->get('environment') ? $env->get('environment') : 'development');