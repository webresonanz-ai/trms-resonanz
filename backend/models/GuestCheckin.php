<?php
require_once __DIR__ . '/../config/database.php';

class GuestCheckin {
    public $conn;
    private $table = 'guest_checkins';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->ensureTableExists();
    }

    private function ensureTableExists() {
        $query = "CREATE TABLE IF NOT EXISTS {$this->table} (
            id INT AUTO_INCREMENT PRIMARY KEY,
            guest_id INT NOT NULL,
            qr_code VARCHAR(100) NOT NULL,
            checked_in_at DATETIME NOT NULL,
            scan_source VARCHAR(50) DEFAULT 'camera',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uniq_guest_checkin (guest_id),
            INDEX idx_guest_id (guest_id),
            INDEX idx_qr_code (qr_code),
            INDEX idx_checked_in_at (checked_in_at),
            CONSTRAINT fk_guest_checkins_guest
                FOREIGN KEY (guest_id) REFERENCES guests(id) ON DELETE CASCADE
        )";

        $this->conn->exec($query);
    }

    public function create($guestId, $qrCode, $checkedInAt, $scanSource = 'camera') {
        $query = "INSERT INTO {$this->table} (guest_id, qr_code, checked_in_at, scan_source)
                  VALUES (:guest_id, :qr_code, :checked_in_at, :scan_source)";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':guest_id' => $guestId,
            ':qr_code' => $qrCode,
            ':checked_in_at' => $checkedInAt,
            ':scan_source' => $scanSource
        ]);
    }

    public function findByGuestId($guestId) {
        $query = "SELECT * FROM {$this->table} WHERE guest_id = :guest_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':guest_id' => $guestId]);
        return $stmt->fetch();
    }

    public function getRecent($limit = 20) {
        $query = "SELECT gc.id, gc.qr_code, gc.checked_in_at, gc.scan_source,
                         g.id AS guest_id, g.name, g.company, g.position
                  FROM {$this->table} gc
                  INNER JOIN guests g ON g.id = gc.guest_id
                  ORDER BY gc.checked_in_at DESC
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
