<?php
class RateLimitMiddleware implements MiddlewareInterface {
    private $maxRequests = 100; // Max requests per time window
    private $timeWindow = 3600; // Time window in seconds (1 hour)
    
    public function handle($request, $next) {
        $ip = $_SERVER['REMOTE_ADDR'];
        $key = "rate_limit_{$ip}";
        
        session_start();
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [
                'count' => 1,
                'first_request' => time()
            ];
        } else {
            $data = $_SESSION[$key];
            $timeElapsed = time() - $data['first_request'];
            
            if ($timeElapsed < $this->timeWindow) {
                if ($data['count'] >= $this->maxRequests) {
                    $this->sendError('Rate limit exceeded. Try again later.', 429);
                }
                $_SESSION[$key]['count']++;
            } else {
                // Reset window
                $_SESSION[$key] = [
                    'count' => 1,
                    'first_request' => time()
                ];
            }
        }
        
        return $next($request);
    }
    
    private function sendError($message, $code) {
        http_response_code($code);
        echo json_encode(['error' => $message]);
        exit();
    }
}
?>