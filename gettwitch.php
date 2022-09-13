<?php

use App\Libraries\Session;

include 'autoload.php';
print_r($_GET);
$session = new Session();
if (key_exists('code', $_GET)) {
    $session->set('codeTwitch', $_GET['code']);
}