<?php
// DIC configuration

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], Monolog\Logger::DEBUG));
    return $logger;
};

$container['layout'] = function($c) {
    $layout = new \Challenge\Middleware\Layout($c->get('renderer'), $c->get('settings')['layout']);
    return $layout;
};

$container['throttlerStorage'] = function($c) {
    return new \Challenge\Throttle\Storage\Session();
};

$container['throttlerRule'] = function($c) {
    $bucketSize = 5;
    $drainRate = 0.25; //requests per second
    return new \Challenge\Throttle\Rule\LeakyBucket($bucketSize, $drainRate);
};

$container['throttler'] = function($c) {
    $throttler = new \Challenge\Throttle\Throttler();
    $throttler->addRule($c['throttlerRule']);
    $throttler->setStorage($c['throttlerStorage']);
    return $throttler;
};