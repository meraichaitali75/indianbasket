<?php

require_once __DIR__ . "/db.php";

// Start the session at the beginning of the script
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Start the session at the beginning of the script
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


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

    // Function for manual registration
    public function register($firstname, $lastname, $email, $password, $confirm_password, $gender)
    {
        $errors = [];
    
        // Input sanitization
        $firstname = $this->sanitize($firstname);
        $lastname = $this->sanitize($lastname);
        $email = $this->sanitize($email);
        $password = trim($password);
        $confirm_password = trim($confirm_password);
        
        // ✅ Set provider for manual registrations
        $provider = "manual"; 
    
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
            // ✅ Check if email already exists
            $stmt = $this->db->conn->prepare("SELECT provider FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
    
            if ($stmt->num_rows > 0) {
                $stmt->bind_result($provider);
                $stmt->fetch();
    
                if ($provider === "google") {
                    return ["You already registered with Google. Please log in with Google."];
                } else {
                    return ["Email is already registered."];
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
    
        // ✅ Encrypt password before inserting
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    
        // ✅ Insert user into database
        $stmt = $this->db->conn->prepare("INSERT INTO users (firstname, lastname, email, password, provider, gender) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $firstname, $lastname, $email, $hashed_password, $provider, $gender);
    
        if ($stmt->execute()) {
            return "success"; // ✅ Ensure `register.php` checks for this
        } else {
            return "Error: " . $stmt->error;
        }
    }
    

    // Function for manual login
    public function login($email, $password)
    {
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
        $stmt = $this->db->conn->prepare("SELECT user_id, password, provider, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
    
        if ($stmt->num_rows == 1) {
            $stmt->bind_result($user_id, $hashed_password, $provider, $role);
            $stmt->fetch();
    
            // Check if user registered with OAuth
            if ($provider !== "manual") {
                return ["You registered using " . ucfirst($provider) . ". Please log in with " . ucfirst($provider) . "."];
            }
    
            // Verify Password
            if (password_verify($password, $hashed_password)) {
                session_regenerate_id(true);
    
                if ($role == 'admin') {
                    $_SESSION["admin_id"] = $user_id;
                    $_SESSION["admin_role"] = $role;
                    return "admin";
                } else {
                    $_SESSION["user_id"] = $user_id;
                    return "user";
                }
            } else {
                return ["Incorrect password."];
            }
        } else {
            return ["No account found with this email."];
        }
    }
    
    // Function to check if a user exists by email
    public function getUserByEmail($email)
    {
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
    public function registerUser($firstname, $lastname, $email, $google_id, $profile_pic, $provider)
    {
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

    public function forgotPassword($email)
    {
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
 public function getDatabaseConnection()
 {
     return $this->db->conn;
 }

 // Fetch user details by user ID
 public function getUserById($user_id)
 {
     $stmt = $this->db->conn->prepare("SELECT * FROM users WHERE user_id = ?");
     $stmt->bind_param("i", $user_id);
     $stmt->execute();
     $result = $stmt->get_result();
     return $result->fetch_assoc();
 }

 // Fetch user Orders by user ID
 public function getUserOrders($user_id)
 {
     $stmt = $this->db->conn->prepare("
     SELECT o.order_id, o.order_date, o.total_amount, o.status, 
            od.product_id, od.quantity, od.price, 
            p.name AS product_name, p.image AS product_image
     FROM orders o
     JOIN orderdetails od ON o.order_id = od.order_id
     JOIN products p ON od.product_id = p.product_id
     WHERE o.user_id = ?
     ORDER BY o.order_date DESC");

     $stmt->bind_param("i", $user_id);
     $stmt->execute();
     $result = $stmt->get_result();

     $orders = [];
     while ($row = $result->fetch_assoc()) {
         $orders[] = $row;
     }
     return $orders;
 }

 // Update user profile
 public function updateProfile($user_id, $firstname, $lastname, $email, $gender, $profile_pic = null)
 {
     // Input sanitization
     $firstname = $this->sanitize($firstname);
     $lastname = $this->sanitize($lastname);
     $email = $this->sanitize($email);
     $gender = $this->sanitize($gender);

     // Validate inputs
     if (!preg_match("/^[a-zA-Z]{2,50}$/", $firstname)) {
         return "First name must be only letters, between 2 to 50 characters.";
     }
     if (!preg_match("/^[a-zA-Z]{2,50}$/", $lastname)) {
         return "Last name must be only letters, between 2 to 50 characters.";
     }
     if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
         return "Invalid email format.";
     }
     if (!in_array($gender, ["Male", "Female", "Other"])) {
         return "Please select a valid gender.";
     }

     // Update user profile in the database
     if ($profile_pic) {
         $stmt = $this->db->conn->prepare("UPDATE users SET firstname = ?, lastname = ?, email = ?, gender = ?, profile_pic = ? WHERE user_id = ?");
         $stmt->bind_param("sssssi", $firstname, $lastname, $email, $gender, $profile_pic, $user_id);
     } else {
         $stmt = $this->db->conn->prepare("UPDATE users SET firstname = ?, lastname = ?, email = ?, gender = ? WHERE user_id = ?");
         $stmt->bind_param("ssssi", $firstname, $lastname, $email, $gender, $user_id);
     }

     if ($stmt->execute()) {
         return "success";
     } else {
         return "Error: " . $stmt->error;
     }
 }

 // Get Billing Address
 public function getBillingAddresses($user_id)
 {
     $stmt = $this->db->conn->prepare("SELECT * FROM billing_addresses WHERE user_id = ?");
     $stmt->bind_param("i", $user_id);
     $stmt->execute();
     $result = $stmt->get_result();

     $addresses = [];
     while ($row = $result->fetch_assoc()) {
         $addresses[] = $row;
     }
     return $addresses;
 }

 // Add Billing address
 public function addBillingAddress($user_id, $firstname, $lastname, $email, $phone, $country, $province, $city, $street_address, $zip_code, $landmark, $address_type) {
    echo "Function addBillingAddress() is being called!<br>"; // ✅ Debug line

    // Debug input values
    echo "<pre>";
    print_r(func_get_args());
    echo "</pre>";

    // Input sanitization
    $firstname = $this->sanitize($firstname);
    $lastname = $this->sanitize($lastname);
    $email = $this->sanitize($email);
    $phone = $this->sanitize($phone);
    $country = $this->sanitize($country);
    $province = $this->sanitize($province);
    $city = $this->sanitize($city);
    $street_address = $this->sanitize($street_address);
    $zip_code = $this->sanitize($zip_code);
    $landmark = $this->sanitize($landmark);
    $address_type = $this->sanitize($address_type);

    $stmt = $this->db->conn->prepare("INSERT INTO billing_addresses (user_id, firstname, lastname, email, phone, country, province, city, street_address, zip_code, landmark, address_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("SQL Error: " . $this->db->conn->error);
    }

    $stmt->bind_param("isssssssssss", $user_id, $firstname, $lastname, $email, $phone, $country, $province, $city, $street_address, $zip_code, $landmark, $address_type);

    if ($stmt->execute()) {
        return "success";
    } else {
        return "SQL Error: " . $stmt->error;
    }
}


 // Edit Billing Address
 public function editBillingAddress($address_id, $firstname, $lastname, $email, $phone, $country, $province, $city, $street_address, $zip_code, $landmark, $address_type)
 {
     // Input sanitization
     $firstname = $this->sanitize($firstname);
     $lastname = $this->sanitize($lastname);
     $email = $this->sanitize($email);
     $phone = $this->sanitize($phone);
     $country = $this->sanitize($country);
     $province = $this->sanitize($province);
     $city = $this->sanitize($city);
     $street_address = $this->sanitize($street_address);
     $zip_code = $this->sanitize($zip_code);
     $landmark = $this->sanitize($landmark);
     $address_type = $this->sanitize($address_type);

     // Update the address in the database
     $stmt = $this->db->conn->prepare("UPDATE billing_addresses SET firstname = ?, lastname = ?, email = ?, phone = ?, country = ?, province = ?, city = ?, street_address = ?, zip_code = ?, landmark = ?, address_type = ? WHERE address_id = ?");
     $stmt->bind_param("sssssssssssi", $firstname, $lastname, $email, $phone, $country, $province, $city, $street_address, $zip_code, $landmark, $address_type, $address_id);

     if ($stmt->execute()) {
         return "success";
     } else {
         return "Error: " . $stmt->error;
     }
 }

 // Delete Billing Address
 public function deleteBillingAddress($address_id)
 {
     $stmt = $this->db->conn->prepare("DELETE FROM billing_addresses WHERE address_id = ?");
     $stmt->bind_param("i", $address_id);

     if ($stmt->execute()) {
         return "success";
     } else {
         return "Error: " . $stmt->error;
     }
 }

 // Function to change user password
 public function changePassword($user_id, $currentPassword, $newPassword, $confirmPassword)
 {
     // Input sanitization
     $currentPassword = trim($currentPassword);
     $newPassword = trim($newPassword);
     $confirmPassword = trim($confirmPassword);

     // Validate inputs
     if (empty($currentPassword)) {
         return "Current password is required.";
     }
     if (empty($newPassword)) {
         return "New password is required.";
     }
     if (empty($confirmPassword)) {
         return "Confirm new password is required.";
     }
     if ($newPassword !== $confirmPassword) {
         return "New passwords do not match.";
     }

     // Check if the new password meets the requirements
     if (!preg_match("/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", $newPassword)) {
         return "New password must be at least 8 characters long, contain an uppercase letter, lowercase letter, a number, and a special character.";
     }

     // Fetch the current password from the database
     $stmt = $this->db->conn->prepare("SELECT password FROM users WHERE user_id = ?");
     $stmt->bind_param("i", $user_id);
     $stmt->execute();
     $stmt->store_result();
     $stmt->bind_result($hashed_password);
     $stmt->fetch();

     // Verify the current password
     if (!password_verify($currentPassword, $hashed_password)) {
         return "Current password is incorrect.";
     }

     // Hash the new password
     $newHashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

     // Update the password in the database
     $stmt = $this->db->conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
     $stmt->bind_param("si", $newHashedPassword, $user_id);

     if ($stmt->execute()) {
         return "success";
     } else {
         return "Error: " . $stmt->error;
     }
 }
}
