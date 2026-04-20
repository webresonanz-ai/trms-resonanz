<?php
require_once __DIR__ . '/../models/Stream.php';

class StreamController {
    private $streamModel;
    
    public function __construct() {
        $this->streamModel = new Stream();
    }
    
    public function create($request) {
        if (!isset($request['user']) || $request['user']['role'] !== 'admin') {
            return $this->sendError('Admin access required', 403);
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$this->validateStream($data)) {
            return $this->sendError('Invalid stream data', 400);
        }
        
        $this->streamModel->track_name = $data['track_name'];
        $this->streamModel->artist = $data['artist'];
        $this->streamModel->stream_date = $data['stream_date'] ?? date('Y-m-d');
        $this->streamModel->stream_count = $data['stream_count'] ?? 0;
        $this->streamModel->revenue = $data['revenue'] ?? 0;
        
        if ($this->streamModel->create()) {
            $stream = $this->streamModel->findById($this->streamModel->id);
            return $this->sendSuccess($stream, 'Stream record created successfully', 201);
        }
        
        return $this->sendError('Failed to create stream record', 500);
    }
    
    public function getStream($request, $id) {
        $stream = $this->streamModel->findById($id);
        
        if (!$stream) {
            return $this->sendError('Stream record not found', 404);
        }
        
        return $this->sendSuccess($stream);
    }
    
    public function getRecentStreams($request) {
        $limit = $_GET['limit'] ?? 10;
        
        $streams = $this->streamModel->getRecent($limit);
        
        return $this->sendSuccess($streams);
    }
    
    public function getAllStreams($request) {
        $limit = $_GET['limit'] ?? 100;
        $offset = $_GET['offset'] ?? 0;
        
        $filters = [];
        if (isset($_GET['artist'])) $filters['artist'] = $_GET['artist'];
        if (isset($_GET['track_name'])) $filters['track_name'] = $_GET['track_name'];
        if (isset($_GET['start_date'])) $filters['start_date'] = $_GET['start_date'];
        if (isset($_GET['end_date'])) $filters['end_date'] = $_GET['end_date'];
        
        $streams = $this->streamModel->getAll($limit, $offset, $filters);
        
        return $this->sendSuccess($streams);
    }
    
    public function deleteStream($request, $id) {
        if (!isset($request['user']) || $request['user']['role'] !== 'admin') {
            return $this->sendError('Admin access required', 403);
        }
        
        $stream = $this->streamModel->findById($id);
        
        if (!$stream) {
            return $this->sendError('Stream record not found', 404);
        }
        
        if ($this->streamModel->delete($id)) {
            return $this->sendSuccess(null, 'Stream record deleted successfully');
        }
        
        return $this->sendError('Failed to delete stream record', 500);
    }
    
    private function validateStream($data) {
        return isset($data['track_name']) && isset($data['artist']);
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