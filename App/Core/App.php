<?php

use App\Config\Helpers;
use App\Libraries\Dotenv;

require_once ROOTDIR . 'autoload.php';
$env = new Dotenv();
$helpers = new Helpers();
$helpers->loadDefaults();
defined('ENVIRONMENT') or define('ENVIRONMENT', $env->get('environment') ? $env->get('environment') : 'development');
if (ENVIRONMENT != 'production')
    include_once 'displayErrors.php';