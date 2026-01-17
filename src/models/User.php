<?php
class User {
    private ?int $id = null;
    private string $email;
    private string $firstName;
    private string $lastName;
    private string $role = 'ROLE_USER';
    private ?string $passwordHash = null;
    private ?DateTime $createdAt = null;
    
    public function __construct() {
        $this->createdAt = new DateTime();
    }
    
    // Getters
    public function getId(): ?int {
        return $this->id;
    }
    
    public function getEmail(): string {
        return $this->email;
    }
    
    public function getFirstName(): string {
        return $this->firstName;
    }
    
    public function getLastName(): string {
        return $this->lastName;
    }
    
    public function getFullName(): string {
        return $this->firstName . ' ' . $this->lastName;
    }
    
    public function getRole(): string {
        return $this->role;
    }
    
    public function getPasswordHash(): ?string {
        return $this->passwordHash;
    }
    
    public function getCreatedAt(): ?DateTime {
        return $this->createdAt;
    }
    
    // Setters
    public function setEmail(string $email): void {
        $this->email = trim($email);
    }
    
    public function setFirstName(string $firstName): void {
        $this->firstName = trim($firstName);
    }
    
    public function setLastName(string $lastName): void {
        $this->lastName = trim($lastName);
    }
    
    public function setRole(string $role): void {
        if (in_array($role, ['ROLE_USER', 'ROLE_ADMIN'])) {
            $this->role = $role;
        }
    }
    
    public function setPassword(string $password): void {
        $this->passwordHash = password_hash($password, PASSWORD_DEFAULT);
    }
    
    public function setPasswordHash(string $hash): void {
        $this->passwordHash = $hash;
    }
    
    // Database methods
    public function save(): bool {
        $database = Database::getInstance();
        
        if ($this->id === null) {
            // Insert new user
            $sql = "INSERT INTO users (email, password_hash, first_name, last_name, role) VALUES (?, ?, ?, ?, ?)";
            $stmt = $database->prepare($sql);
            $stmt->bind_param("sssss", 
                $this->email, 
                $this->passwordHash, 
                $this->firstName, 
                $this->lastName, 
                $this->role
            );
            
            if ($stmt->execute()) {
                $this->id = $database->getLastInsertId();
                $stmt->close();
                return true;
            }
            $stmt->close();
        } else {
            // Update existing user
            $sql = "UPDATE users SET email = ?, first_name = ?, last_name = ?, role = ? WHERE id = ?";
            $stmt = $database->prepare($sql);
            $stmt->bind_param("ssssi", 
                $this->email, 
                $this->firstName, 
                $this->lastName, 
                $this->role, 
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
    
    public static function findByEmail(string $email): ?User {
        $database = Database::getInstance();
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $database->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $user = new self();
            $user->id = $row['id'];
            $user->email = $row['email'];
            $user->firstName = $row['first_name'];
            $user->lastName = $row['last_name'];
            $user->role = $row['role'];
            $user->passwordHash = $row['password_hash'];
            $user->createdAt = new DateTime($row['created_at']);
            $stmt->close();
            return $user;
        }
        
        $stmt->close();
        return null;
    }
    
    public static function findById(int $id): ?User {
        $database = Database::getInstance();
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $database->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $user = new self();
            $user->id = $row['id'];
            $user->email = $row['email'];
            $user->firstName = $row['first_name'];
            $user->lastName = $row['last_name'];
            $user->role = $row['role'];
            $user->passwordHash = $row['password_hash'];
            $user->createdAt = new DateTime($row['created_at']);
            $stmt->close();
            return $user;
        }
        
        $stmt->close();
        return null;
    }
    
    public function verifyPassword(string $password): bool {
        return password_verify($password, $this->passwordHash);
    }
    
    public function validate(): array {
        $errors = [];
        
        // Email validation
        if (empty($this->email)) {
            $errors[] = "L'email est obligatoire";
        } elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'email n'est pas valide";
        } elseif (strlen($this->email) > 100) {
            $errors[] = "L'email ne doit pas dépasser 100 caractères";
        }
        
        // First name validation
        if (empty($this->firstName)) {
            $errors[] = "Le prénom est obligatoire";
        } elseif (strlen($this->firstName) > 50) {
            $errors[] = "Le prénom ne doit pas dépasser 50 caractères";
        }
        
        // Last name validation
        if (empty($this->lastName)) {
            $errors[] = "Le nom est obligatoire";
        } elseif (strlen($this->lastName) > 50) {
            $errors[] = "Le nom ne doit pas dépasser 50 caractères";
        }
        
        return $errors;
    }
}