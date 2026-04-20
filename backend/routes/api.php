<?php
class Router {
    private $routes = [];
    private $middlewares = [];
    
    public function addMiddleware($middleware) {
        $this->middlewares[] = $middleware;
    }
    
    public function addRoute($method, $path, $handler) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }
    
    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Apply global middlewares
        $request = [];
        $next = function($request) use ($method, $uri) {
            return $this->handleRoute($method, $uri, $request);
        };
        
        // Chain middlewares
        for ($i = count($this->middlewares) - 1; $i >= 0; $i--) {
            $middleware = $this->middlewares[$i];
            $currentNext = $next;
            $next = function($request) use ($middleware, $currentNext) {
                return $middleware->handle($request, $currentNext);
            };
        }
        
        return $next($request);
    }
    
    private function handleRoute($method, $uri, $request) {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;
            
            // Check for route parameters
            $pattern = preg_replace('/\{[a-z]+\}/', '([a-zA-Z0-9-]+)', $route['path']);
            $pattern = '#^' . $pattern . '$#';
            
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                $handler = $route['handler'];
                
                if (is_array($handler) && count($handler) === 2) {
                    $controller = new $handler[0]();
                    $method = $handler[1];
                    return $controller->$method($request, ...$matches);
                }
            }
        }
        
        http_response_code(404);
        echo json_encode(['error' => 'Route not found']);
    }
}

// Define routes
$router = new Router();

// Add global middlewares
$router->addMiddleware(new CorsMiddleware());
$router->addMiddleware(new LoggerMiddleware());
$router->addMiddleware(new RateLimitMiddleware());
$router->addMiddleware(new AuthMiddleware());

// User routes
$router->addRoute('POST', '/api/register', ['UserController', 'register']);
$router->addRoute('POST', '/api/login', ['UserController', 'login']);
$router->addRoute('GET', '/api/profile', ['UserController', 'getProfile']);
$router->addRoute('PUT', '/api/profile', ['UserController', 'updateProfile']);
$router->addRoute('GET', '/api/users', ['UserController', 'getAllUsers']);

// Product routes
$router->addRoute('POST', '/api/products', ['ProductController', 'create']);
$router->addRoute('GET', '/api/products', ['ProductController', 'getAllProducts']);
$router->addRoute('GET', '/api/products/{id}', ['ProductController', 'getProduct']);
$router->addRoute('PUT', '/api/products/{id}', ['ProductController', 'updateProduct']);
$router->addRoute('DELETE', '/api/products/{id}', ['ProductController', 'deleteProduct']);

// Artist routes
$router->addRoute('POST', '/api/artists', ['ArtistController', 'create']);
$router->addRoute('GET', '/api/artists', ['ArtistController', 'getAllArtists']);
$router->addRoute('GET', '/api/artists/{id}', ['ArtistController', 'getArtist']);
$router->addRoute('PUT', '/api/artists/{id}', ['ArtistController', 'updateArtist']);
$router->addRoute('DELETE', '/api/artists/{id}', ['ArtistController', 'deleteArtist']);
$router->addRoute('GET', '/api/dashboard/stats', ['ArtistController', 'getDashboardStats']);

// Album routes
$router->addRoute('POST', '/api/albums', ['AlbumController', 'create']);
$router->addRoute('GET', '/api/albums', ['AlbumController', 'getAllAlbums']);
$router->addRoute('GET', '/api/albums/{id}', ['AlbumController', 'getAlbum']);
$router->addRoute('PUT', '/api/albums/{id}', ['AlbumController', 'updateAlbum']);
$router->addRoute('DELETE', '/api/albums/{id}', ['AlbumController', 'deleteAlbum']);
$router->addRoute('GET', '/api/artists/{artistId}/albums', ['AlbumController', 'getAlbumsByArtist']);

// Stream routes
$router->addRoute('POST', '/api/streams', ['StreamController', 'create']);
$router->addRoute('GET', '/api/streams', ['StreamController', 'getAllStreams']);
$router->addRoute('GET', '/api/streams/recent', ['StreamController', 'getRecentStreams']);
$router->addRoute('GET', '/api/streams/{id}', ['StreamController', 'getStream']);
$router->addRoute('DELETE', '/api/streams/{id}', ['StreamController', 'deleteStream']);

// Guest routes
$router->addRoute('POST', '/api/guests', ['GuestController', 'register']);
$router->addRoute('GET', '/api/guests', ['GuestController', 'getAllGuests']);
$router->addRoute('GET', '/api/guests/stats', ['GuestController', 'getGuestStats']);
$router->addRoute('GET', '/api/guests/{id}/invitation', ['GuestController', 'generateInvitation']);
$router->addRoute('GET', '/api/guests/{id}', ['GuestController', 'getGuest']);
$router->addRoute('GET', '/api/guests/qr/{qrCode}', ['GuestController', 'getGuestByQrCode']);
$router->addRoute('PUT', '/api/guests/{id}', ['GuestController', 'updateGuest']);
$router->addRoute('DELETE', '/api/guests/{id}', ['GuestController', 'deleteGuest']);

return $router;
?>