<?php

use App\Controllers\HomeController;
use App\Controllers\StreamerController;
use App\Controllers\TwitchController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;



$app = AppFactory::create();

$app->get('/', function (Request $request, Response $response, $args) {
    $homeController = new HomeController();
    $homeController->index();
    exit;
});

$app->group('/streamer', function (\Slim\Routing\RouteCollectorProxy $app) {
    $app->get('/sub-list', function (Request $request, Response $response, $args) {
        $streamerController = new StreamerController();
        $response->getBody()->write($streamerController->subList());
        return $response;
    });
    $app->get('/viewers-list', function (Request $request, Response $response, $args) {
        $streamerController = new StreamerController();
        $response->getBody()->write($streamerController->viewersList());
        return $response;
    });
});

$app->group('/twitch', function (\Slim\Routing\RouteCollectorProxy $app) {
    $app->get('/authorize', function (Request $request, Response $response, $args) {
        $twitchController = new TwitchController();
        $response->getBody()->write($twitchController->authorize());
        return $response;
    });

    $app->get('/authorize-complete', function (Request $request, Response $response, $args) {
        $twitchController = new TwitchController();
        $response->getBody()->write($twitchController->authorizeComplete());
        return $response;
    });
});



$app->run();