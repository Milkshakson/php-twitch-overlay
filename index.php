<?php
defined('ROOTDIR') or define('ROOTDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);
include_once 'displayErrors.php';
require_once('App/Core/App.php');

use App\Controllers\StreamerController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require ROOTDIR . 'vendor/autoload.php';

$app = AppFactory::create();

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello world!");
    return $response;
});

$app->group('/streamer', function (\Slim\Routing\RouteCollectorProxy $app) {
    $app->get('/sub-list', function (Request $request, Response $response, $args) {
        $streamerController = new StreamerController();
        $response->getBody()->write($streamerController->subList());
        return $response;
    });
    $app->get('/viwers-list', function (Request $request, Response $response, $args) {
        $streamerController = new StreamerController();
        $response->getBody()->write($streamerController->viewersList());
        return $response;
    });
});

$app->group('/twitch', function (\Slim\Routing\RouteCollectorProxy $app) {
    $app->get('/authorize', function (Request $request, Response $response, $args) {
        $streamerController = new StreamerController();
        $response->getBody()->write($streamerController->authorize());
        return $response;
    });

    $app->get('/authorize-complete', function (Request $request, Response $response, $args) {
        $streamerController = new StreamerController();
        $response->getBody()->write($streamerController->authorizeComplete());
        return $response;
    });
});



$app->run();