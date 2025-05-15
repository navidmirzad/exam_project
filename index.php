<?php
require_once 'src/Router/Router.php';
require_once 'src/Controllers/ArtistController.php';
use src\Router\Router;
use src\Controllers\ArtistController;

header('Content-Type: application/json');

$router = new Router();

$router->get('/artists', [new ArtistController(), 'getAll']);
$router->get('/artists/{artist_id}', [new ArtistController(), 'getOne']);
$router->post('/artists', [new ArtistController(), 'create']);
$router->delete('/artists/{artist_id}', [new ArtistController(), 'delete']);

$router->run();