<?php
require_once __DIR__ . '/../models/Album.php';

class AlbumController {
    private $albumModel;
    
    public function __construct() {
        $this->albumModel = new Album();
    }
    
    public function create($request) {
        if (!isset($request['user']) || $request['user']['role'] !== 'admin') {
            return $this->sendError('Admin access required', 403);
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$this->validateAlbum($data)) {
            return $this->sendError('Invalid album data', 400);
        }
        
        $this->albumModel->title = $data['title'];
        $this->albumModel->artist = $data['artist'];
        $this->albumModel->artist_id = $data['artist_id'];
        $this->albumModel->cover = $data['cover'] ?? '';
        $this->albumModel->release_date = $data['release_date'] ?? date('Y-m-d');
        $this->albumModel->streams = $data['streams'] ?? 0;
        $this->albumModel->revenue = $data['revenue'] ?? 0;
        
        if ($this->albumModel->create()) {
            $album = $this->albumModel->findById($this->albumModel->id);
            return $this->sendSuccess($album, 'Album created successfully', 201);
        }
        
        return $this->sendError('Failed to create album', 500);
    }
    
    public function getAlbum($request, $id) {
        $album = $this->albumModel->findById($id);
        
        if (!$album) {
            return $this->sendError('Album not found', 404);
        }
        
        return $this->sendSuccess($album);
    }
    
    public function getAlbumsByArtist($request, $artistId) {
        $albums = $this->albumModel->findByArtist($artistId);
        
        return $this->sendSuccess($albums);
    }
    
    public function getAllAlbums($request) {
        $limit = $_GET['limit'] ?? 100;
        $offset = $_GET['offset'] ?? 0;
        
        $filters = [];
        if (isset($_GET['artist_id'])) $filters['artist_id'] = $_GET['artist_id'];
        if (isset($_GET['artist'])) $filters['artist'] = $_GET['artist'];
        
        $albums = $this->albumModel->getAll($limit, $offset, $filters);
        
        return $this->sendSuccess($albums);
    }
    
    public function updateAlbum($request, $id) {
        if (!isset($request['user']) || $request['user']['role'] !== 'admin') {
            return $this->sendError('Admin access required', 403);
        }
        
        $album = $this->albumModel->findById($id);
        
        if (!$album) {
            return $this->sendError('Album not found', 404);
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if ($this->albumModel->update($id, $data)) {
            $updatedAlbum = $this->albumModel->findById($id);
            return $this->sendSuccess($updatedAlbum, 'Album updated successfully');
        }
        
        return $this->sendError('Failed to update album', 500);
    }
    
    public function deleteAlbum($request, $id) {
        if (!isset($request['user']) || $request['user']['role'] !== 'admin') {
            return $this->sendError('Admin access required', 403);
        }
        
        $album = $this->albumModel->findById($id);
        
        if (!$album) {
            return $this->sendError('Album not found', 404);
        }
        
        if ($this->albumModel->delete($id)) {
            return $this->sendSuccess(null, 'Album deleted successfully');
        }
        
        return $this->sendError('Failed to delete album', 500);
    }
    
    private function validateAlbum($data) {
        return isset($data['title']) && isset($data['artist']) && isset($data['artist_id']);
    }
    
    private function sendSuccess($data, $message = '', $code = 200) {
        http_response_code($code);
        echo json_encode([
            'success' => true,
            'message' => $message,
            'data' => $data
        ]);
        return true;
    }
    
    private function sendError($message, $code = 400) {
        http_response_code($code);
        echo json_encode([
            'success' => false,
            'error' => $message
        ]);
        return false;
    }
}