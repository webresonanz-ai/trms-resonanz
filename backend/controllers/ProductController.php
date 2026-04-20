<?php
require_once __DIR__ . '/../models/Product.php';

class ProductController {
    private $productModel;
    
    public function __construct() {
        $this->productModel = new Product();
    }
    
    public function create($request) {
        if (!isset($request['user']) || !isset($request['user']['id'])) {
            return $this->sendError('Authentication required', 401);
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$this->validateProduct($data)) {
            return $this->sendError('Invalid product data', 400);
        }
        
        $this->productModel->name = $data['name'];
        $this->productModel->description = $data['description'];
        $this->productModel->price = $data['price'];
        $this->productModel->stock = $data['stock'];
        $this->productModel->user_id = $request['user']['id'];
        
        if ($this->productModel->create()) {
            $product = $this->productModel->findById($this->productModel->id);
            return $this->sendSuccess($product, 'Product created successfully', 201);
        }
        
        return $this->sendError('Failed to create product', 500);
    }
    
    public function getProduct($request, $id) {
        $product = $this->productModel->findById($id);
        
        if (!$product) {
            return $this->sendError('Product not found', 404);
        }
        
        return $this->sendSuccess($product);
    }
    
    public function updateProduct($request, $id) {
        if (!isset($request['user']) || !isset($request['user']['id'])) {
            return $this->sendError('Authentication required', 401);
        }
        
        $product = $this->productModel->findById($id);
        
        if (!$product) {
            return $this->sendError('Product not found', 404);
        }
        
        if ($product['user_id'] != $request['user']['id'] && $request['user']['role'] !== 'admin') {
            return $this->sendError('Unauthorized to update this product', 403);
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if ($this->productModel->update($id, $data)) {
            $updatedProduct = $this->productModel->findById($id);
            return $this->sendSuccess($updatedProduct, 'Product updated successfully');
        }
        
        return $this->sendError('Failed to update product', 500);
    }
    
    public function deleteProduct($request, $id) {
        if (!isset($request['user']) || !isset($request['user']['id'])) {
            return $this->sendError('Authentication required', 401);
        }
        
        $product = $this->productModel->findById($id);
        
        if (!$product) {
            return $this->sendError('Product not found', 404);
        }
        
        if ($product['user_id'] != $request['user']['id'] && $request['user']['role'] !== 'admin') {
            return $this->sendError('Unauthorized to delete this product', 403);
        }
        
        if ($this->productModel->delete($id, $request['user']['id'])) {
            return $this->sendSuccess(null, 'Product deleted successfully');
        }
        
        return $this->sendError('Failed to delete product', 500);
    }
    
    public function getAllProducts($request) {
        $limit = $_GET['limit'] ?? 100;
        $offset = $_GET['offset'] ?? 0;
        
        $filters = [];
        if (isset($_GET['user_id'])) $filters['user_id'] = $_GET['user_id'];
        if (isset($_GET['min_price'])) $filters['min_price'] = $_GET['min_price'];
        if (isset($_GET['max_price'])) $filters['max_price'] = $_GET['max_price'];
        
        $products = $this->productModel->getAll($limit, $offset, $filters);
        
        return $this->sendSuccess($products);
    }
    
    private function validateProduct($data) {
        return isset($data['name']) && isset($data['price']) && 
               is_numeric($data['price']) && $data['price'] >= 0 &&
               (!isset($data['stock']) || is_numeric($data['stock']));
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