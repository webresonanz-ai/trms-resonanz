<?php
require_once __DIR__ . '/../models/Guest.php';

class GuestController {
    private $guestModel;
    
    public function __construct() {
        $this->guestModel = new Guest();
    }
    
    public function register($request) {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$this->validateRegistration($data)) {
            return $this->sendError('Invalid input data. Name is required.', 400);
        }
        
        $registrationDate = date('Y-m-d H:i:s');
        $tempQrCode = 'REG-TEMP-' . time();
        
        $this->guestModel->name = $data['name'];
        $this->guestModel->company = $data['company'] ?? null;
        $this->guestModel->position = $data['position'] ?? null;
        $this->guestModel->notes = $data['notes'] ?? null;
        $this->guestModel->registration_date = $registrationDate;
        $this->guestModel->qr_code = $tempQrCode;
        
        if ($this->guestModel->create()) {
            $qrCode = Guest::generateQrCode($this->guestModel->id, $registrationDate);
            $this->guestModel->qr_code = $qrCode;
            
            $updateQuery = "UPDATE {$this->guestModel->table} SET qr_code = :qr_code WHERE id = :id";
            $stmt = $this->guestModel->conn->prepare($updateQuery);
            $stmt->bindParam(':qr_code', $qrCode);
            $stmt->bindParam(':id', $this->guestModel->id);
            $stmt->execute();
            
            return $this->sendSuccess([
                'guest' => [
                    'id' => $this->guestModel->id,
                    'name' => $this->guestModel->name,
                    'company' => $this->guestModel->company,
                    'position' => $this->guestModel->position,
                    'notes' => $this->guestModel->notes,
                    'qr_code' => $qrCode,
                    'registration_date' => $this->guestModel->registration_date
                ]
            ], 'Guest registered successfully', 201);
        }
        
        return $this->sendError('Registration failed', 500);
    }
    
    public function getGuest($request, $id) {
        if (!$this->guestModel->findById($id)) {
            return $this->sendError('Guest not found', 404);
        }
        
        return $this->sendSuccess([
            'id' => $this->guestModel->id,
            'name' => $this->guestModel->name,
            'company' => $this->guestModel->company,
            'position' => $this->guestModel->position,
            'notes' => $this->guestModel->notes,
            'qr_code' => $this->guestModel->qr_code,
            'registration_date' => $this->guestModel->registration_date,
            'created_at' => $this->guestModel->created_at
        ]);
    }
    
    public function getGuestByQrCode($request, $qrCode) {
        if (!$this->guestModel->findByQrCode($qrCode)) {
            return $this->sendError('Guest not found', 404);
        }
        
        return $this->sendSuccess([
            'id' => $this->guestModel->id,
            'name' => $this->guestModel->name,
            'company' => $this->guestModel->company,
            'position' => $this->guestModel->position,
            'notes' => $this->guestModel->notes,
            'qr_code' => $this->guestModel->qr_code,
            'registration_date' => $this->guestModel->registration_date,
            'created_at' => $this->guestModel->created_at
        ]);
    }
    
    public function getAllGuests($request) {
        $limit = $_GET['limit'] ?? 100;
        $offset = $_GET['offset'] ?? 0;
        
        $guests = $this->guestModel->getAll($limit, $offset);
        
        return $this->sendSuccess($guests);
    }
    
    public function updateGuest($request, $id) {
        if (!$this->guestModel->findById($id)) {
            return $this->sendError('Guest not found', 404);
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if ($this->guestModel->update($id, $data)) {
            $this->guestModel->findById($id);
            return $this->sendSuccess([
                'id' => $this->guestModel->id,
                'name' => $this->guestModel->name,
                'company' => $this->guestModel->company,
                'position' => $this->guestModel->position,
                'notes' => $this->guestModel->notes,
                'qr_code' => $this->guestModel->qr_code,
                'registration_date' => $this->guestModel->registration_date,
                'created_at' => $this->guestModel->created_at
            ], 'Guest updated successfully');
        }
        
        return $this->sendError('Update failed', 500);
    }
    
    public function deleteGuest($request, $id) {
        if (!$this->guestModel->findById($id)) {
            return $this->sendError('Guest not found', 404);
        }

        if ($this->guestModel->delete($id)) {
            return $this->sendSuccess([], 'Guest deleted successfully');
        }

        return $this->sendError('Delete failed', 500);
    }

    public function generateInvitation($request, $id) {
        // Find guest
        if (!$this->guestModel->findById($id)) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Guest not found']);
            return;
        }

        $name = $this->guestModel->name;
        $company = $this->guestModel->company ?? '';
        $position = $this->guestModel->position ?? '';
        $qrCode = $this->guestModel->qr_code;

        // Template path
        $templatePath = realpath(__DIR__ . '/../templates/images/undangan.png');
        if (!$templatePath || !file_exists($templatePath)) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Invitation template not found']);
            return;
        }

        // A4 dimensions in mm
        $pageWidth = 210;
        $pageHeight = 297;

        // Create new PDF
        $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->SetMargins(0, 0, 0, true);
        $pdf->SetAutoPageBreak(false, 0);
        $pdf->AddPage();

        // Place background template image (full page)
        $pdf->Image($templatePath, 0, 0, $pageWidth, $pageHeight, '', '', '', false, 300, '', false, false, 0);

        // Set text color (black)
        $pdf->SetTextColor(0, 0, 0);

        // Name (larger, bold)
        $pdf->SetFont('dejavusans', 'B', 34);
        $nameY = $pageHeight * 0.16; // adjust this multiplier based on template design
        $pdf->SetXY(0, $nameY);
        $pdf->Cell($pageWidth, 12, $name, 0, 1, 'C');

        // Company (medium)
        if (!empty($company)) {
            $pdf->SetFont('dejavusans', '', 24);
            $companyY = $pageHeight * 0.24;
            $pdf->SetXY(0, $companyY);
            $pdf->Cell($pageWidth, 10, $company, 0, 1, 'C');
        }

        // Position (medium)
        if (!empty($position)) {
            $pdf->SetFont('dejavusans', '', 24);
            $positionY = $pageHeight * 0.28;
            $pdf->SetXY(0, $positionY);
            $pdf->Cell($pageWidth, 10, $position, 0, 1, 'C');
        }

        // QR Code
        $qrSize = 62; // mm
        $qrX = ($pageWidth - $qrSize) / 2; // center horizontally
        $qrY = $pageHeight * 0.415; // adjust as needed
        $pdf->write2DBarcode($qrCode, 'QRCODE,H', $qrX, $qrY, $qrSize, $qrSize, ['border' => true, 'vborder' => 2, 'hborder' => 2], 'N');

        // Output PDF as download
        $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $name);
        $filename = "invitation_{$id}_{$safeName}.pdf";
        $pdf->Output($filename, 'D');
        exit;
    }

    public function getGuestStats($request) {
        $totalGuests = $this->guestModel->countAll();

        return $this->sendSuccess([
            'total_guests' => $totalGuests
        ]);
    }
    
    private function validateRegistration($data) {
        return isset($data['name']) && strlen($data['name']) >= 1;
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