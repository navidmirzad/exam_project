# Exam Project API Documentation

## Overview

This RESTful API allows you to manage artists, albums, tracks, media types, genres, and playlists. All responses are in JSON. Use the endpoints below with tools like Postman or curl.

---

## Artists

- **GET /artists**  
  Get all artists.

- **GET /artists/search/{name}**  
  Search for an artist by name.

- **GET /artists/{artist_id}**  
  Get artist by ID.

- **POST /artists**  
  Create a new artist.  
  **Body (JSON):**
  ```json
  { "name": "Artist Name" }
  ```

- **DELETE /artists/{artist_id}**  
  Delete an artist (only if they have no albums).

---

## Albums

- **GET /albums**  
  Get all albums.

- **GET /albums/search/{title}**  
  Search albums by title.

- **GET /albums/{album_id}**  
  Get album by ID.

- **GET /albums/{album_id}/tracks**  
  Get album and its tracks.

- **POST /albums**  
  Create a new album.  
  **Body (JSON):**
  ```json
  { "title": "Album Title", "artist_id": 1 }
  ```

- **PUT /albums/{album_id}**  
  Update an album.  
  **Body (JSON):**
  ```json
  { "title": "New Title", "artist_id": 1 }
  ```

- **DELETE /albums/{album_id}**  
  Delete an album (only if it has no tracks).

---

## Tracks

- **GET /tracks/search/{search}**  
  Search tracks by name.

- **GET /tracks/{track_id}**  
  Get track by ID.

- **GET /tracks/composer/{composer}**  
  Get tracks by composer.

- **POST /tracks**  
  Create a new track.  
  **Body (JSON):**
  ```json
  {
    "name": "Track Name",
    "album_id": 1,
    "media_type_id": 1,
    "genre_id": 1,
    "composer": "Composer Name",
    "milliseconds": 200000,
    "bytes": 1234567,
    "unit_price": 0.99
  }
  ```

- **PUT /tracks/{track_id}**  
  Update a track (any field).  
  **Body (JSON):**
  ```json
  {
    "name": "New Name",
    "album_id": 1
    // ...other fields as needed
  }
  ```

- **DELETE /tracks/{track_id}**  
  Delete a track (only if not in a playlist).

---

## Media Types

- **GET /media-types**  
  Get all media types.

---

## Genres

- **GET /genres**  
  Get all genres.

---

## Playlists

- **GET /playlists**  
  Get all playlists.

- **GET /playlists/search/{name}**  
  Search playlists by name.

- **GET /playlists/{playlist_id}**  
  Get playlist by ID (includes tracks).

- **POST /playlists**  
  Create a new playlist.  
  **Body (JSON):**
  ```json
  { "name": "Playlist Name" }
  ```

- **POST /playlists/{playlist_id}/tracks**  
  Add a track to a playlist.  
  **Body (JSON):**
  ```json
  { "track_id": 1 }
  ```

- **DELETE /playlists/{playlist_id}/tracks/{track_id}**  
  Remove a track from a playlist.

- **DELETE /playlists/{playlist_id}**  
  Delete a playlist (only if it has no tracks).

---

## Example Postman Request

**POST /tracks**

- URL: `http://localhost/exam_project/tracks`
- Method: `POST`
- Headers: `Content-Type: application/json`
- Body (raw, JSON):
  ```json
  {
    "name": "Test Track",
    "album_id": 1,
    "media_type_id": 1,
    "genre_id": 1,
    "composer": "Test Composer",
    "milliseconds": 200000,
    "bytes": 1234567,
    "unit_price": 0.99
  }
  ```

---

## Notes

- All endpoints return JSON.
- All errors are returned as JSON with an `error` field.
- Use valid IDs from your database for foreign keys.
- Make sure your API is running and accessible from your Postman/curl environment.

