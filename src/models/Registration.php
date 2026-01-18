<?php
class Registration
{
    private ?int $id = null;
    private int $eventId;
    private int $userId;
    private DateTime $registrationDate;
    private ?string $eventTitle = null;
    private ?DateTime $eventDate = null;
    private ?string $eventLocation = null;

    public function __construct(int $eventId, int $userId)
    {
        $this->eventId = $eventId;
        $this->userId = $userId;
        $this->registrationDate = new DateTime();
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEventId(): int
    {
        return $this->eventId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getRegistrationDate(): DateTime
    {
        return $this->registrationDate;
    }

    public function getEventTitle(): ?string
    {
        return $this->eventTitle;
    }

    public function getEventDate(): ?DateTime
    {
        return $this->eventDate;
    }

    public function getEventLocation(): ?string
    {
        return $this->eventLocation;
    }

    // Setters
    public function setEventTitle(string $title): void
    {
        $this->eventTitle = $title;
    }

    public function setEventDate(DateTime $date): void
    {
        $this->eventDate = $date;
    }

    public function setEventLocation(string $location): void
    {
        $this->eventLocation = $location;
    }

    // Database methods
    public function save(): bool
    {
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
        $sql = "INSERT INTO registrations (event_id, user_id) VALUES (?, ?)";
        $stmt = $database->prepare($sql);
        $stmt->bind_param("ii", $this->eventId, $this->userId);

        if ($stmt->execute()) {
            $this->id = $database->getLastInsertId();
            $stmt->close();
            return true;
        }

        $stmt->close();
        return false;
    }

    public static function isUserRegistered(int $eventId, int $userId): bool
    {
        $database = Database::getInstance();
        $sql = "SELECT COUNT(*) as count FROM registrations WHERE event_id = ? AND user_id = ?";
        $stmt = $database->prepare($sql);
        $stmt->bind_param("ii", $eventId, $userId);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return $row['count'] > 0;
    }

    public static function hasAvailableCapacity(int $eventId): bool
    {
        $database = Database::getInstance();
        $sql = "SELECT e.capacity, COUNT(r.id) as registered 
                FROM events e 
                LEFT JOIN registrations r ON e.id = r.event_id 
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

    public static function findByUser(int $userId): array
    {
        $database = Database::getInstance();
        $sql = "SELECT r.*, e.title, e.event_date, e.location 
                FROM registrations r 
                JOIN events e ON r.event_id = e.id 
                WHERE r.user_id = ? 
                ORDER BY e.event_date ASC";
        $stmt = $database->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();

        $registrations = [];
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $registration = new self($row['event_id'], $row['user_id']);
            $registration->id = $row['id'];
            $registration->registrationDate = new DateTime($row['registration_date']);
            $registration->eventTitle = $row['title'];
            $registration->eventDate = new DateTime($row['event_date']);
            $registration->eventLocation = $row['location'];
            $registrations[] = $registration;
        }

        $stmt->close();
        return $registrations;
    }

    public static function findByEvent(int $eventId): array
    {
        $database = Database::getInstance();
        $sql = "SELECT r.*, u.first_name, u.last_name, u.email 
                FROM registrations r 
                JOIN users u ON r.user_id = u.id 
                WHERE r.event_id = ? 
                ORDER BY r.registration_date ASC";
        $stmt = $database->prepare($sql);
        $stmt->bind_param("i", $eventId);
        $stmt->execute();

        $registrations = [];
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $registration = new self($row['event_id'], $row['user_id']);
            $registration->id = $row['id'];
            $registration->registrationDate = new DateTime($row['registration_date']);
            $registrations[] = $registration;
        }

        $stmt->close();
        return $registrations;
    }

    public function delete(): bool
    {
        if ($this->id === null) {
            return false;
        }

        $database = Database::getInstance();
        $sql = "DELETE FROM registrations WHERE id = ?";
        $stmt = $database->prepare($sql);
        $stmt->bind_param("i", $this->id);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        $stmt->close();
        return false;
    }

    // Magic properties for event details
    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        return null;
    }
}
