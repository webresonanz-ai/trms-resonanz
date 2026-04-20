<?php
require_once __DIR__ . '/../models/Artist.php';

class ArtistController {
    private $artistModel;
    
    public function __construct() {
        $this->artistModel = new Artist();
    }
    
    public function create($request) {
        if (!isset($request['user']) || $request['user']['role'] !== 'admin') {
            return $this->sendError('Admin access required', 403);
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$this->validateArtist($data)) {
            return $this->sendError('Invalid artist data', 400);
        }
        
        $this->artistModel->name = $data['name'];
        $this->artistModel->image = $data['image'] ?? '';
        $this->artistModel->monthly_listeners = $data['monthly_listeners'] ?? 0;
        $this->artistModel->total_streams = $data['total_streams'] ?? 0;
        $this->artistModel->albums_count = $data['albums_count'] ?? 0;
        $this->artistModel->genre = $data['genre'] ?? '';
        $this->artistModel->revenue = $data['revenue'] ?? 0;
        
        if ($this->artistModel->create()) {
            $artist = $this->artistModel->findById($this->artistModel->id);
            return $this->sendSuccess($artist, 'Artist created successfully', 201);
        }
        
        return $this->sendError('Failed to create artist', 500);
    }
    
    public function getArtist($request, $id) {
        $artist = $this->artistModel->findById($id);
        
        if (!$artist) {
            return $this->sendError('Artist not found', 404);
        }
        
        return $this->sendSuccess($artist);
    }
    
    public function getAllArtists($request) {
        $limit = $_GET['limit'] ?? 100;
        $offset = $_GET['offset'] ?? 0;
        
        $filters = [];
        if (isset($_GET['genre'])) $filters['genre'] = $_GET['genre'];
        if (isset($_GET['search'])) $filters['search'] = $_GET['search'];
        
        $artists = $this->artistModel->getAll($limit, $offset, $filters);
        
        return $this->sendSuccess($artists);
    }
    
    public function updateArtist($request, $id) {
        if (!isset($request['user']) || $request['user']['role'] !== 'admin') {
            return $this->sendError('Admin access required', 403);
        }
        
        $artist = $this->artistModel->findById($id);
        
        if (!$artist) {
            return $this->sendError('Artist not found', 404);
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if ($this->artistModel->update($id, $data)) {
            $updatedArtist = $this->artistModel->findById($id);
            return $this->sendSuccess($updatedArtist, 'Artist updated successfully');
        }
        
        return $this->sendError('Failed to update artist', 500);
    }
    
    public function deleteArtist($request, $id) {
        if (!isset($request['user']) || $request['user']['role'] !== 'admin') {
            return $this->sendError('Admin access required', 403);
        }
        
        $artist = $this->artistModel->findById($id);
        
        if (!$artist) {
            return $this->sendError('Artist not found', 404);
        }
        
        if ($this->artistModel->delete($id)) {
            return $this->sendSuccess(null, 'Artist deleted successfully');
        }
        
        return $this->sendError('Failed to delete artist', 500);
    }
    
    public function getDashboardStats($request) {
        $stats = $this->artistModel->getStats();
        
        $stats['growth'] = 23.5;
        
        return $this->sendSuccess($stats);
    }
    
    private function validateArtist($data) {
        return isset($data['name']) && is_string($data['name']);
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