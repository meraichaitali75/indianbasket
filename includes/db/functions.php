<?php
require_once "./includes/db/config.php";

class Functions
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    // Helper function for input validation
    private function sanitize($input)
    {
        return htmlspecialchars(strip_tags(trim($input)));
    }

    // Function for registration
    public function register($firstname, $lastname, $email, $password, $confirm_password, $gender)
    {
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
            $stmt = $this->db->conn->prepare("SELECT user_id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $errors[] = "Email is already registered.";
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
        $stmt = $this->db->conn->prepare("INSERT INTO users (user_id, firstname, lastname, email, password, gender) VALUES (NULL, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $firstname, $lastname, $email, $hashed_password, $gender);

        if ($stmt->execute()) {
            return "success";
        } else {
            return "Error: " . $stmt->error; // Show SQL error message
        }
    }

    public function login($email, $password)
    {
        $errors = [];

        // ** Trim and Sanitize Input**
        $email = trim($email);
        $password = trim($password);

        // ** Email Validation**
        if (empty($email)) {
            $errors[] = "Email is required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        }

        // ** Password Validation (Same as Registration)**
        if (empty($password)) {
            $errors[] = "Password is required.";
        } elseif (
            strlen($password) < 8 ||
            !preg_match('/[A-Z]/', $password) ||
            !preg_match('/[a-z]/', $password) ||
            !preg_match('/[0-9]/', $password) ||
            !preg_match('/[\W]/', $password)
        ) {
            $errors[] = "Password must be at least 8 characters long and include an uppercase letter, a lowercase letter, a number, and a special character.";
        }

        // ** If Validation Fails, Return Errors**
        if (!empty($errors)) {
            return $errors;
        }

        // ** Check if Email Exists in the Database**
        $stmt = $this->db->conn->prepare("SELECT user_id, password FROM users WHERE email = ?");
        if (!$stmt) {
            return ["Database error. Please try again."];
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $stmt->bind_result($user_id, $hashed_password);
            $stmt->fetch();

            // **Verify Password**
            if (password_verify($password, $hashed_password)) {
                // ** Secure Session Handling**
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
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
}
