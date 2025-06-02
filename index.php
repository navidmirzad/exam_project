<?php
require_once 'src/Logger/RequestLogger.php';
require_once 'src/Router/Router.php';
require_once 'src/Controllers/ArtistController.php';
require_once 'src/Controllers/AlbumController.php';
require_once 'src/Controllers/TrackController.php';
require_once 'src/Controllers/MediaTypeController.php';
require_once 'src/Controllers/GenreController.php';
require_once 'src/Controllers/PlaylistController.php';

use src\Controllers\AlbumController;
use src\Router\Router;
use src\Controllers\ArtistController;
use src\Controllers\TrackController;
use src\Controllers\MediaTypeController;
use src\Controllers\GenreController;
use src\Controllers\PlaylistController;
use src\Logger\RequestLogger;

header('Content-Type: application/json');

RequestLogger::log();

$router = new Router();

$router->get('/', function() {
    echo json_encode(['message' => 'Welcome to my Mandatory II / Exam Project - RestAPIs on Chinook_AutoIncrement Database.']);
});

// Artists routes
$router->get('/artists', [new ArtistController(), 'getAll']);
$router->get('/artists/{artist_id}', [new ArtistController(), 'getById']);
$router->post('/artists', [new ArtistController(), 'create']);
$router->delete('/artists/{artist_id}', [new ArtistController(), 'delete']);

// Album routes
$router->get('/albums', [new AlbumController(), 'getAll']);
$router->get('/albums/{album_id}', [new AlbumController(), 'getById']);
$router->get('/albums/{album_id}/tracks', [new AlbumController(), 'getAlbumAndTracks']);
$router->post('/albums', [new AlbumController(), 'create']);
$router->put('/albums/{album_id}', [new AlbumController(), 'update']);
$router->delete('/albums/{album_id}', [new AlbumController(), 'delete']);

// Tracks routes
// search for: name, media type & genre
$router->get('/tracks', [new TrackController(), 'search']); 
$router->get('/tracks/{track_id}', [new TrackController(), 'getById']);
$router->get('/tracks/composer/{composer}', [new TrackController(), 'getByComposer']);
$router->post('/tracks', [new TrackController(), 'create']);
$router->put('/tracks/{track_id}', [new TrackController(), 'update']);
$router->delete('/tracks/{track_id}', [new TrackController(), 'delete']);

// MediaTypes and Genres routes
$router->get('/media-types', [new MediaTypeController(), 'getAll']);
$router->get('/genres', [new GenreController(), 'getAll']);

// Playlist routes
$router->get('/playlists', [new PlaylistController(), 'getAll']);
$router->get('/playlists/{playlist_id}', [new PlaylistController(), 'getById']);
$router->post('/playlists', [new PlaylistController(), 'create']);
$router->post('/playlists/{playlist_id}/tracks', [new PlaylistController(), 'addTrack']);
$router->delete('/playlists/{playlist_id}/tracks/{track_id}', [new PlaylistController(), 'removeTrack']);
$router->delete('/playlists/{playlist_id}', [new PlaylistController(), 'delete']);

$router->run();