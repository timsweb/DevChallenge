<?php
// Routes
$app->get('/', function ($request, $response, $args) {
    $args['nextUrl'] = $this->get('router')->pathFor('play');
    return $this->renderer->render($response, 'index.phtml', $args);
});

$app->get('/play', function($request, $response, $args) {
   if (isset($_GET['throttled'])) {
       $args['throttled'] = filter_var($_GET['throttled'], FILTER_VALIDATE_BOOLEAN);
   }
   return $this->renderer->render($response, 'play.phtml', $args);
})->setName('play');

$app->any('/doEvent', function($request, $response, $args)  use ($app){
    $throttler = $this->get('throttler');
    if (isset($_POST['blocking'])) {
        $this->logger->info("Doing event");
        return $throttler->throttleRequest('testEvent', function() use ($response){
            $this->logger->info('Event done.');
            return $response->withStatus(302)->withHeader('Location', '/play');
        });
    } else {
        if ($throttler->throttled('testEvent')) {
            $this->logger->info('Doing event');
            $throttler->log('testEvent');
            return $response->withStatus(302)->withHeader('Location', '/play?throttled=true');
        } else {
            $this->logger->info('Event not done');
            return $response->withStatus(302)->withHeader('Location', '/play?throttled=false');
        }
    }
})->setName('doEvent');
