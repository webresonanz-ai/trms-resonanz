<?php
require_once __DIR__ . '/../models/User.php';

class UserController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    public function register($request) {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$this->validateRegistration($data)) {
            return $this->sendError('Invalid input data', 400);
        }
        
        // Check if user already exists
        if ($this->userModel->findByEmail($data['email'])) {
            return $this->sendError('User already exists', 409);
        }
        
        $this->userModel->name = $data['name'];
        $this->userModel->email = $data['email'];
        $this->userModel->password = $data['password'];
        $this->userModel->role = $data['role'] ?? 'user';
        
        if ($this->userModel->create()) {
            $token = $this->generateToken($this->userModel->id, $this->userModel->email);
            return $this->sendSuccess([
                'user' => [
                    'id' => $this->userModel->id,
                    'name' => $this->userModel->name,
                    'email' => $this->userModel->email,
                    'role' => $this->userModel->role
                ],
                'token' => $token
            ], 'Registration successful', 201);
        }
        
        return $this->sendError('Registration failed', 500);
    }
    
    public function login($request) {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['email']) || !isset($data['password'])) {
            return $this->sendError('Email and password required', 400);
        }
        
        if (!$this->userModel->findByEmail($data['email'])) {
            return $this->sendError('Invalid credentials', 401);
        }
        
        if (!password_verify($data['password'], $this->userModel->password)) {
            return $this->sendError('Invalid credentials', 401);
        }
        
        $token = $this->generateToken($this->userModel->id, $this->userModel->email);
        
        return $this->sendSuccess([
            'user' => [
                'id' => $this->userModel->id,
                'name' => $this->userModel->name,
                'email' => $this->userModel->email,
                'role' => $this->userModel->role
            ],
            'token' => $token
        ], 'Login successful');
    }
    
    public function getProfile($request) {
        if (!isset($request['user']) || !isset($request['user']['id'])) {
            return $this->sendError('User not authenticated', 401);
        }
        
        $user = $this->userModel->findById($request['user']['id']);
        
        if (!$user) {
            return $this->sendError('User not found', 404);
        }
        
        return $this->sendSuccess($user);
    }
    
    public function updateProfile($request) {
        if (!isset($request['user']) || !isset($request['user']['id'])) {
            return $this->sendError('User not authenticated', 401);
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        $userId = $request['user']['id'];
        
        if ($this->userModel->update($userId, $data)) {
            $updatedUser = $this->userModel->findById($userId);
            return $this->sendSuccess($updatedUser, 'Profile updated successfully');
        }
        
        return $this->sendError('Update failed', 500);
    }
    
    public function getAllUsers($request) {
        $limit = $_GET['limit'] ?? 100;
        $offset = $_GET['offset'] ?? 0;
        
        $users = $this->userModel->getAll($limit, $offset);
        
        return $this->sendSuccess($users);
    }
    
    private function generateToken($userId, $email) {
        $header = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        $payload = base64_encode(json_encode([
            'user_id' => $userId,
            'email' => $email,
            'exp' => time() + 86400 // 24 hours
        ]));
        $signature = hash_hmac('sha256', "$header.$payload", $_ENV['JWT_SECRET'], true);
        $signature = base64_encode($signature);
        
        return "$header.$payload.$signature";
    }
    
    private function validateRegistration($data) {
        return isset($data['name']) && isset($data['email']) && isset($data['password']) &&
               strlen($data['name']) >= 2 && filter_var($data['email'], FILTER_VALIDATE_EMAIL) &&
               strlen($data['password']) >= 6;
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
?>