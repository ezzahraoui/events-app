<?php
class Event
{
    private ?int $id = null;
    private string $title;
    private string $description;
    private DateTime $eventDate;
    private string $location;
    private int $capacity;
    private ?string $imageUrl = null;
    private ?int $createdBy = null;
    private ?DateTime $createdAt = null;
    private ?DateTime $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getEventDate(): DateTime
    {
        return $this->eventDate;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function getCapacity(): int
    {
        return $this->capacity;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function getCreatedBy(): ?int
    {
        return $this->createdBy;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    // Setters
    public function setTitle(string $title): void
    {
        $this->title = trim($title);
        $this->updatedAt = new DateTime();
    }

    public function setDescription(string $description): void
    {
        $this->description = trim($description);
        $this->updatedAt = new DateTime();
    }

    public function setEventDate(DateTime $date): void
    {
        $this->eventDate = $date;
        $this->updatedAt = new DateTime();
    }

    public function setLocation(string $location): void
    {
        $this->location = trim($location);
        $this->updatedAt = new DateTime();
    }

    public function setCapacity(int $capacity): void
    {
        $this->capacity = $capacity;
        $this->updatedAt = new DateTime();
    }

    public function setImageUrl(?string $url): void
    {
        $this->imageUrl = $url;
        $this->updatedAt = new DateTime();
    }

    public function setCreatedBy(int $userId): void
    {
        $this->createdBy = $userId;
        $this->updatedAt = new DateTime();
    }

    // Database methods
    public function save(): bool
    {
        $database = Database::getInstance();

        if ($this->id === null) {
            // Insert new event
            $sql = "INSERT INTO events (title, description, event_date, location, capacity, image_url, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $database->prepare($sql);

            $eventDateStr = $this->eventDate->format('Y-m-d H:i:s');
            $imageUrl = $this->imageUrl;  // Handle NULL properly
            $createdBy = $this->createdBy;
            $stmt->bind_param(
                "sssissi",
                $this->title,
                $this->description,
                $eventDateStr,
                $this->location,
                $this->capacity,
                $imageUrl,
                $createdBy
            );

            if ($stmt->execute()) {
                $this->id = $database->getLastInsertId();
                $stmt->close();
                return true;
            }
            $stmt->close();
        } else {
            // Update existing event
            $sql = "UPDATE events SET title = ?, description = ?, event_date = ?, location = ?, capacity = ?, image_url = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
            $stmt = $database->prepare($sql);

            $eventDateStr = $this->eventDate->format('Y-m-d H:i:s');
            $imageUrl = $this->imageUrl;  // Handle NULL properly
            $stmt->bind_param(
                "sssissi",
                $this->title,
                $this->description,
                $eventDateStr,
                $this->location,
                $this->capacity,
                $imageUrl,
                $this->id
            );

            if ($stmt->execute()) {
                $stmt->close();
                return true;
            }
            $stmt->close();
        }

        return false;
    }

    public static function findById(int $id): ?Event
    {
        $database = Database::getInstance();
        $sql = "SELECT * FROM events WHERE id = ?";
        $stmt = $database->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $event = new self();
            $event->id = $row['id'];
            $event->title = $row['title'];
            $event->description = $row['description'];
            $event->eventDate = new DateTime($row['event_date']);
            $event->location = $row['location'];
            $event->capacity = $row['capacity'];
            $event->imageUrl = $row['image_url'];
            $event->createdBy = $row['created_by'];
            $event->createdAt = new DateTime($row['created_at']);
            $event->updatedAt = new DateTime($row['updated_at']);
            $stmt->close();
            return $event;
        }

        $stmt->close();
        return null;
    }

    public static function findAll(): array
    {
        $database = Database::getInstance();
        $sql = "SELECT * FROM events ORDER BY event_date ASC";
        $result = $database->query($sql);

        $events = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $event = new self();
                $event->id = $row['id'];
                $event->title = $row['title'];
                $event->description = $row['description'];
                $event->eventDate = new DateTime($row['event_date']);
                $event->location = $row['location'];
                $event->capacity = $row['capacity'];
                $event->imageUrl = $row['image_url'];
                $event->createdBy = $row['created_by'];
                $event->createdAt = new DateTime($row['created_at']);
                $event->updatedAt = new DateTime($row['updated_at']);
                $events[] = $event;
            }
        }

        return $events;
    }

    public static function findAllPublished(): array
    {
        $database = Database::getInstance();
        $sql = "SELECT * FROM events ORDER BY event_date ASC";
        $result = $database->query($sql);

        $events = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $event = new self();
                $event->id = $row['id'];
                $event->title = $row['title'];
                $event->description = $row['description'];
                $event->eventDate = new DateTime($row['event_date']);
                $event->location = $row['location'];
                $event->capacity = $row['capacity'];
                $event->imageUrl = $row['image_url'];
                $event->createdBy = $row['created_by'];
                $event->createdAt = new DateTime($row['created_at']);
                $event->updatedAt = new DateTime($row['updated_at']);
                $events[] = $event;
            }
        }

        return $events;
    }

    public function delete(): bool
    {
        if ($this->id === null) {
            return false;
        }

        $database = Database::getInstance();
        $sql = "DELETE FROM events WHERE id = ?";
        $stmt = $database->prepare($sql);
        $stmt->bind_param("i", $this->id);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        $stmt->close();
        return false;
    }

    public function validate(): array
    {
        $errors = [];

        // Title validation
        if (empty($this->title)) {
            $errors[] = "Le titre est obligatoire";
        } elseif (strlen($this->title) < 3) {
            $errors[] = "Le titre doit contenir au moins 3 caractères";
        } elseif (strlen($this->title) > 200) {
            $errors[] = "Le titre ne doit pas dépasser 200 caractères";
        }

        // Description validation
        if (empty($this->description)) {
            $errors[] = "La description est obligatoire";
        } elseif (strlen($this->description) < 10) {
            $errors[] = "La description doit contenir au moins 10 caractères";
        }

        // Location validation
        if (empty($this->location)) {
            $errors[] = "Le lieu est obligatoire";
        } elseif (strlen($this->location) > 200) {
            $errors[] = "Le lieu ne doit pas dépasser 200 caractères";
        }

        // Capacity validation
        if (!is_numeric($this->capacity) || $this->capacity <= 0) {
            $errors[] = "La capacité doit être un nombre positif";
        } elseif ($this->capacity > 10000) {
            $errors[] = "La capacité ne peut pas dépasser 10000 personnes";
        }

        // Event date validation
        // Only validate future date if it's a new event (no ID)
        if ($this->id === null) {
            $now = new DateTime();
            // Add 1 minute buffer to avoid timezone/timing issues
            $now->modify('+1 minute');
            if ($this->eventDate < $now) {
                $errors[] = "La date de l'événement doit être dans le futur";
            }
        }

        return $errors;
    }
}
