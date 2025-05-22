<?php
require_once 'src/Router/Router.php';
require_once 'src/Controllers/ArtistController.php';
require_once 'src/Controllers/AlbumController.php';
require_once 'src/Controllers/TrackController.php';


use src\Controllers\AlbumController;
use src\Router\Router;
use src\Controllers\ArtistController;
use src\Controllers\TrackController;

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

// Tracks routes
// search for: name, media type & genre
$router->get('/tracks/{search}', [new TrackController(), 'search']); 
$router->get('/tracks/{track_id}', [new TrackController(), 'getById']);
$router->post('/tracks', [new TrackController(), 'create']);
$router->put('/tracks/{track_id}', [new TrackController(), 'update']);
$router->delete('/tracks/{track_id}', [new TrackController(), 'delete']);

// to check

/* 
GET 	tracks?s=<search_text>	 	Retrieves tracks whose name includes the search text, including their media types and genres
GET	tracks/<track_id>	 	Retrieves one track, including its media type and genre
GET	tracks?composer=<composer>	 	Retrieves tracks by a specific composer
POST	tracks	name, album_id, media_type_id, genre_id, composer, milliseconds, bytes, unit_price	Creates a track
PUT	tracks/<track_id>	name?, album_id?, media_type_id?, genre_id?, composer?, milliseconds?, bytes?, unit_price?	Edits track information
DELETE	tracks/<track_id>	 	Deletes a track, only if it does not belong to a playlist
*/


$router->run();