<?php
require_once 'src/Router/Router.php';
require_once 'src/Controllers/ArtistController.php';
require_once 'src/Controllers/AlbumController.php';


use src\Controllers\AlbumController;
use src\Router\Router;
use src\Controllers\ArtistController;

header('Content-Type: application/json');

$router = new Router();

// Artists routes
$router->get('/artists', [new ArtistController(), 'getAll']);
$router->get('/artists/search/{name}', [new ArtistController(), 'search']);
$router->get('/artists/{artist_id}', [new ArtistController(), 'getById']);
$router->post('/artists', [new ArtistController(), 'create']);
$router->delete('/artists/{artist_id}', [new ArtistController(), 'delete']);

// Album routes
$router->get('/albums', [new AlbumController(), 'getAll']);
$router->get('/albums/search/{title}', [new AlbumController(), 'search']);
$router->get('/albums/{album_id}', [new AlbumController(), 'getById']);
$router->get('/albums/{album_id}/tracks', [new AlbumController(), 'getAlbumAndTracks']);
$router->post('/albums', [new AlbumController(), 'create']);
$router->put('/albums/{album_id}', [new AlbumController(), 'update']);
$router->delete('/albums/{album_id}', [new AlbumController(), 'delete']);

// to check


$router->run();