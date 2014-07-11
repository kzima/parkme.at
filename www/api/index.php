<?php

require __DIR__.'/../vendor/autoload.php';

$app = new Slim\Slim();

$app->get('/users', function() use ($app) {
    $users = User::all()->toJson();
    $app->response->headers->set('Content-Type', 'application/json');
    $app->response->setBody($users);
});

$app->run();
