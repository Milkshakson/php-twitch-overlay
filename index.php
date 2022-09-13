<?php
defined('ROOTDIR') or define('ROOTDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);
include_once 'displayErrors.php';
require_once('App/Core/App.php');

use App\Controllers\ViewersController;

$controller = new ViewersController();
$controller->index();