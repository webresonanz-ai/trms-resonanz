<?php
class LoggerMiddleware implements MiddlewareInterface {
    private $logFile;
    
    public function __construct() {
        $this->logFile = __DIR__ . '/../logs/api.log';
        if (!file_exists(dirname($this->logFile))) {
            mkdir(dirname($this->logFile), 0777, true);
        }
    }
    
    public function handle($request, $next) {
        $startTime = microtime(true);
        
        // Log request
        $this->logRequest();
        
        $response = $next($request);
        
        // Log response time
        $duration = round((microtime(true) - $startTime) * 1000, 2);
        $this->logResponseTime($duration);
        
        return $response;
    }
    
    private function logRequest() {
        $log = [
            'timestamp' => date('Y-m-d H:i:s'),
            'method' => $_SERVER['REQUEST_METHOD'],
            'path' => $_SERVER['REQUEST_URI'],
            'ip' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
        ];
        
        $this->writeLog('REQUEST', $log);
    }
    
    private function logResponseTime($duration) {
        $log = [
            'timestamp' => date('Y-m-d H:i:s'),
            'duration_ms' => $duration,
            'path' => $_SERVER['REQUEST_URI']
        ];
        
        $this->writeLog('RESPONSE', $log);
    }
    
    private function writeLog($type, $data) {
        $logEntry = "[{$type}] " . json_encode($data) . PHP_EOL;
        file_put_contents($this->logFile, $logEntry, FILE_APPEND);
    }
}
?>