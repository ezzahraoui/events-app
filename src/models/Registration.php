<?php
class Registration {
    private ?int $id = null;
    private int $eventId;
    private int $userId;
    private DateTime $registrationDate;
    private string $status = 'confirmed';
    
    // Additional properties for joined data
    private ?string $eventTitle = null;
    private ?DateTime $eventDate = null;
    private ?string $eventLocation = null;
    private ?string $userFirstName = null;
    private ?string $userLastName = null;
    private ?string $userEmail = null;
    
    public function __construct(int $eventId, int $userId) {
        $this->eventId = $eventId;
        $this->userId = $userId;
        $this->registrationDate = new DateTime();
    }
    
    // Getters
    public function getId(): ?int {
        return $this->id;
    }
    
    public function getEventId(): int {
        return $this->eventId;
    }
    
    public function getUserId(): int {
        return $this->userId;
    }
    
    public function getRegistrationDate(): DateTime {
        return $this->registrationDate;
    }
    
    public function getStatus(): string {
        return $this->status;
    }
    
    // Setters
    public function setStatus(string $status): void {
        if (in_array($status, ['confirmed', 'cancelled'])) {
            $this->status = $status;
        }
    }
    
    // Database methods
    public function save(): bool {
        $database = Database::getInstance();
        
        // Check if already registered
        if (self::isUserRegistered($this->eventId, $this->userId)) {
            return false;
        }
        
        // Check event capacity
        if (!self::hasAvailableCapacity($this->eventId)) {
            return false;
        }
        
        // Insert registration
        $sql = "INSERT INTO registrations (event_id, user_id, status) VALUES (?, ?, ?)";
        $stmt = $database->prepare($sql);
        $stmt->bind_param("iis", $this->eventId, $this->userId, $this->status);
        
        if ($stmt->execute()) {
            $this->id = $database->getLastInsertId();
            $stmt->close();
            return true;
        }
        
        $stmt->close();
        return false;
    }
    
    public static function isUserRegistered(int $eventId, int $userId): bool {
        $database = Database::getInstance();
        $sql = "SELECT COUNT(*) as count FROM registrations WHERE event_id = ? AND user_id = ? AND status = 'confirmed'";
        $stmt = $database->prepare($sql);
        $stmt->bind_param("ii", $eventId, $userId);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        return $row['count'] > 0;
    }
    
    public static function hasAvailableCapacity(int $eventId): bool {
        $database = Database::getInstance();
        $sql = "SELECT e.capacity, COUNT(r.id) as registered 
                FROM events e 
                LEFT JOIN registrations r ON e.id = r.event_id AND r.status = 'confirmed' 
                WHERE e.id = ? 
                GROUP BY e.id, e.capacity";
        $stmt = $database->prepare($sql);
        $stmt->bind_param("i", $eventId);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        if (!$row) {
            return false;
        }
        
        return $row['registered'] < $row['capacity'];
    }
    
    public static function findByUser(int $userId): array {
        $database = Database::getInstance();
        $sql = "SELECT r.*, e.title, e.event_date, e.location 
                FROM registrations r 
                JOIN events e ON r.event_id = e.id 
                WHERE r.user_id = ? AND r.status = 'confirmed' 
                ORDER BY e.event_date ASC";
        $stmt = $database->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        
        $registrations = [];
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $registration = new self($row['event_id'], $row['user_id']);
            $registration->id = $row['id'];
            $registration->status = $row['status'];
            $registration->registrationDate = new DateTime($row['registration_date']);
            $registration->eventTitle = $row['title'];
            $registration->eventDate = new DateTime($row['event_date']);
            $registration->eventLocation = $row['location'];
            $registrations[] = $registration;
        }
        
        $stmt->close();
        return $registrations;
    }
    
    public static function findByEvent(int $eventId): array {
        $database = Database::getInstance();
        $sql = "SELECT r.*, u.first_name, u.last_name, u.email 
                FROM registrations r 
                JOIN users u ON r.user_id = u.id 
                WHERE r.event_id = ? AND r.status = 'confirmed' 
                ORDER BY r.registration_date ASC";
        $stmt = $database->prepare($sql);
        $stmt->bind_param("i", $eventId);
        $stmt->execute();
        
        $registrations = [];
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $registration = new self($row['event_id'], $row['user_id']);
            $registration->id = $row['id'];
            $registration->status = $row['status'];
            $registration->registrationDate = new DateTime($row['registration_date']);
            $registration->userFirstName = $row['first_name'];
            $registration->userLastName = $row['last_name'];
            $registration->userEmail = $row['email'];
            $registrations[] = $registration;
        }
        
        $stmt->close();
        return $registrations;
    }
    
    public function delete(): bool {
        if ($this->id === null) {
            return false;
        }
        
        $database = Database::getInstance();
        $sql = "UPDATE registrations SET status = 'cancelled' WHERE id = ?";
        $stmt = $database->prepare($sql);
        $stmt->bind_param("i", $this->id);
        
        if ($stmt->execute()) {
            $this->status = 'cancelled';
            $stmt->close();
            return true;
        }
        
        $stmt->close();
        return false;
    }
    
    // Magic properties for event details
    public function __get($name) {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        return null;
    }
}