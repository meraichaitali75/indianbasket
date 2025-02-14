<?php
require_once "./includes/db/db.php";

class Functions {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Helper function for input validation
    private function sanitize($input) {
        return htmlspecialchars(strip_tags(trim($input)));
    }

    // Function for manual registration
    public function register($firstname, $lastname, $email, $password, $confirm_password, $gender) {
        $errors = [];

        // Input sanitization
        $firstname = $this->sanitize($firstname);
        $lastname = $this->sanitize($lastname);
        $email = $this->sanitize($email);
        $password = trim($password);
        $confirm_password = trim($confirm_password);

        // Name validation
        if (!preg_match("/^[a-zA-Z]{2,50}$/", $firstname)) {
            $errors[] = "First name must be only letters, between 2 to 50 characters.";
        }
        if (!preg_match("/^[a-zA-Z]{2,50}$/", $lastname)) {
            $errors[] = "Last name must be only letters, between 2 to 50 characters.";
        }

        // Email validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        } else {
            // Check if email already exists
            $stmt = $this->db->conn->prepare("SELECT provider FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($provider);
                $stmt->fetch();

                if ($provider === "google") {
                    $errors[] = "You already registered with Google. Please log in with Google.";
                } else {
                    $errors[] = "Email is already registered.";
                }
            }
            $stmt->close();
        }

        // Password validation
        if (!preg_match("/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", $password)) {
            $errors[] = "Password must be at least 8 characters long, contain an uppercase letter, lowercase letter, a number, and a special character.";
        }

        // Confirm password validation
        if ($password !== $confirm_password) {
            $errors[] = "Passwords do not match.";
        }

        // Gender validation
        if (!in_array($gender, ["Male", "Female", "Other"])) {
            $errors[] = "Please select a valid gender.";
        }

        // If there are validation errors, return them
        if (!empty($errors)) {
            return $errors;
        }

        // Encrypt password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Insert user into database
        $stmt = $this->db->conn->prepare("INSERT INTO users (firstname, lastname, email, google_id, profile_pic, provider, gender) VALUES (?, ?, ?, ?, ?, ?, NULL)");
        $stmt->bind_param("sssss", $firstname, $lastname, $email, $hashed_password, $gender);

        if ($stmt->execute()) {
            return "success";
        } else {
            return "Error: " . $stmt->error;
        }
    }

    // Function for manual login
    public function login($email, $password) {
        $errors = [];

        // Trim and Sanitize Input
        $email = trim($email);
        $password = trim($password);

        // Email Validation
        if (empty($email)) {
            $errors[] = "Email is required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        }

        // Password Validation
        if (empty($password)) {
            $errors[] = "Password is required.";
        }

        // If Validation Fails, Return Errors
        if (!empty($errors)) {
            return $errors;
        }

        // Check if Email Exists in the Database
        $stmt = $this->db->conn->prepare("SELECT user_id, password, provider FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $stmt->bind_result($user_id, $hashed_password, $provider);
            $stmt->fetch();

            // Check if user registered with OAuth
            if ($provider !== "manual") {
                return ["You registered using " . ucfirst($provider) . ". Please log in with " . ucfirst($provider) . "."];
            }

            // Verify Password
            if (password_verify($password, $hashed_password)) {
                session_start();
                session_regenerate_id(true);
                $_SESSION["user_id"] = $user_id;
                return "success";
            } else {
                return ["Incorrect password."];
            }
        } else {
            return ["No account found with this email."];
        }
    }

    // Function to check if a user exists by email
    public function getUserByEmail($email) {
        $stmt = $this->db->conn->prepare("SELECT user_id, firstname, lastname, email, profile_pic, provider FROM users WHERE email = ?");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Function to register a new user via Google OAuth
    public function registerUser($firstname, $lastname, $email, $google_id, $profile_pic, $provider) {
        // Check if user already exists
        $existing_user = $this->getUserByEmail($email);

        if ($existing_user) {
            // If the user is already registered manually, prevent duplicate Google signups
            if ($existing_user['provider'] === 'manual') {
                return "manual_account_exists";
            }

            // If already registered with Google, return existing user ID
            return $existing_user['user_id'];
        }

        // Register new Google user (with NULL password)
        $stmt = $this->db->conn->prepare("INSERT INTO users (firstname, lastname, email, google_id, profile_pic, provider, password) VALUES (?, ?, ?, ?, ?, ?, NULL)");
        $stmt->bind_param("ssssss", $firstname, $lastname, $email, $google_id, $profile_pic, $provider);

        if ($stmt->execute()) {
            return $this->db->conn->insert_id; // Return new user ID
        }
        return false;
    }

    public function forgotPassword($email) {
        // Check if user exists
        $stmt = $this->db->conn->prepare("SELECT provider FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
    
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($provider);
            $stmt->fetch();
            $stmt->close();
    
            // If user registered via social media, do not allow reset
            if ($provider !== "manual") {
                return "Password reset is not available for " . ucfirst($provider) . " accounts.";
            }
    
            //  If user exists, redirect to reset password page
            $_SESSION['reset_email'] = $email; // Store email in session
            header("Location: resetpassword.php");
            exit();
        } else {
            return "Email not found.";
        }
    }

    // For reset password
public function getDatabaseConnection() {
    return $this->db->conn;
}

    
    
}
?>
