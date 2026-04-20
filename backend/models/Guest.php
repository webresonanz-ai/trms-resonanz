<?php
require_once __DIR__ . '/../config/database.php';

class Guest {
    public $conn;
    public $table = 'guests';
    
    public $id;
    public $name;
    public $company;
    public $position;
    public $notes;
    public $qr_code;
    public $registration_date;
    public $created_at;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    public function create() {
        $query = "INSERT INTO {$this->table} (name, company, position, notes, qr_code, registration_date) 
                  VALUES (:name, :company, :position, :notes, :qr_code, :registration_date)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':company', $this->company);
        $stmt->bindParam(':position', $this->position);
        $stmt->bindParam(':notes', $this->notes);
        $stmt->bindParam(':qr_code', $this->qr_code);
        $stmt->bindParam(':registration_date', $this->registration_date);
        
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    public function findById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch();
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->company = $row['company'];
            $this->position = $row['position'];
            $this->notes = $row['notes'];
            $this->qr_code = $row['qr_code'];
            $this->registration_date = $row['registration_date'];
            $this->created_at = $row['created_at'];
            return true;
        }
        
        return false;
    }
    
    public function findByQrCode($qrCode) {
        $query = "SELECT * FROM {$this->table} WHERE qr_code = :qr_code LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':qr_code', $qrCode);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch();
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->company = $row['company'];
            $this->position = $row['position'];
            $this->notes = $row['notes'];
            $this->qr_code = $row['qr_code'];
            $this->registration_date = $row['registration_date'];
            $this->created_at = $row['created_at'];
            return true;
        }
        
        return false;
    }
    
    public function update($id, $data) {
        $fields = [];
        $params = [':id' => $id];
        
        if (isset($data['name'])) {
            $fields[] = "name = :name";
            $params[':name'] = $data['name'];
        }
        
        if (isset($data['company'])) {
            $fields[] = "company = :company";
            $params[':company'] = $data['company'];
        }
        
        if (isset($data['position'])) {
            $fields[] = "position = :position";
            $params[':position'] = $data['position'];
        }
        
        if (isset($data['notes'])) {
            $fields[] = "notes = :notes";
            $params[':notes'] = $data['notes'];
        }
        
        if (empty($fields)) return false;
        
        $query = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute($params);
    }
    
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    public function getAll($limit = 100, $offset = 0) {
        $query = "SELECT * FROM {$this->table} ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM {$this->table}";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'];
    }
    
    public static function generateQrCode($guestId, $registrationDate) {
        $timestamp = strtotime($registrationDate);
        $random = strtoupper(self::randomString(4));
        return "REG-{$guestId}-{$timestamp}-{$random}";
    }
    
    private static function randomString($length) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $result;
    }
}
?>
