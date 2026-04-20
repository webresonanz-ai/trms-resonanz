<?php
require_once __DIR__ . '/../config/database.php';

class AuthMiddleware implements MiddlewareInterface {
    private $excludedRoutes = ['/api/login', '/api/register', '/api/artists', '/api/albums', '/api/streams', '/api/dashboard/stats', '/api/guests'];
    
    public function handle($request, $next) {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Skip auth for excluded routes
        if (in_array($path, $this->excludedRoutes)) {
            return $next($request);
        }
        
        // Check for Authorization header
        $headers = getallheaders();
        
        if (!isset($headers['Authorization'])) {
            $this->sendError('Authorization header missing', 401);
        }
        
        $token = str_replace('Bearer ', '', $headers['Authorization']);
        
        if (!$this->validateToken($token)) {
            $this->sendError('Invalid or expired token', 401);
        }
        
        // Add user info to request
        $request['user'] = $this->getUserFromToken($token);
        
        return $next($request);
    }
    
    private function validateToken($token) {
        try {
            $parts = explode('.', $token);
            if (count($parts) !== 3) return false;
            
            $payload = json_decode(base64_decode($parts[1]), true);
            
            if (!$payload || !isset($payload['exp']) || $payload['exp'] < time()) {
                return false;
            }
            
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    private function getUserFromToken($token) {
        $parts = explode('.', $token);
        $payload = json_decode(base64_decode($parts[1]), true);
        
        $database = new Database();
        $db = $database->getConnection();
        
        $query = "SELECT id, name, email, role FROM users WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->execute([':id' => $payload['user_id']]);
        
        return $stmt->fetch();
    }
    
    private function sendError($message, $code) {
        http_response_code($code);
        echo json_encode(['error' => $message]);
        exit();
    }
}
?>