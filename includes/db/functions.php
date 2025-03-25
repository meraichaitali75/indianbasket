<?php

require_once __DIR__ . "/db.php";

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
    //Get active banners 
    public function getAllBanners()
    {
        $stmt = $this->db->conn->prepare("SELECT * FROM banners ORDER BY created_at DESC");
        if ($stmt === false) {
            error_log("Prepare failed: " . $this->db->conn->error);
            return [];
        }

        if (!$stmt->execute()) {
            error_log("Execute failed: " . $stmt->error);
            return [];
        }

        $result = $stmt->get_result();
        $banners = [];

        while ($row = $result->fetch_assoc()) {
            $banners[] = $row;
        }

        return $banners;
    }

    // Add Billing address
    public function addBillingAddress($user_id, $firstname, $lastname, $email, $phone, $country, $province, $city, $street_address, $zip_code, $landmark, $address_type)
    {
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

    // Function to display all categories
    public function getAllCategories()
    {
        $stmt = $this->db->conn->prepare("SELECT * FROM categories");
        $stmt->execute();
        $result = $stmt->get_result();

        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
        return $categories;
    }

    // Fetch latest 5 categories
    public function getLatestCategories($limit = 5)
    {
        $sql = "SELECT * FROM categories ORDER BY category_id DESC LIMIT $limit";
        $result = $this->db->conn->query($sql);
        $categories = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $categories[] = $row;
            }
        }
        return $categories;
    }

    // Function to display all products
    public function getAllProducts()
    {
        $stmt = $this->db->conn->prepare("
        SELECT p.*, c.name AS category_name 
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.category_id
    ");
        $stmt->execute();
        $result = $stmt->get_result();

        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        return $products;
    }

    // Function to fetch products by category ID
    public function getProductsByCategory($category_id)
    {
        $stmt = $this->db->conn->prepare("
        SELECT p.*, c.name AS category_name 
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.category_id
        WHERE p.category_id = ?
    ");
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        return $products;
    }
    // Function to fetch all products for the shop page
    public function getProducts()
    {
        $sql = "SELECT p.*, c.name AS category_name 
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.category_id";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        return $products;
    }

    // Function to fetch product details by ID
    public function getProductById($product_id)
    {
        $stmt = $this->db->conn->prepare("
             SELECT p.*, c.name AS category_name 
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.category_id
             WHERE p.product_id = ?
         ");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Function to fetch related products by category ID
    public function getRelatedProducts($category_id, $exclude_product_id)
    {
        $stmt = $this->db->conn->prepare("
        SELECT p.*, c.name AS category_name 
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.category_id
        WHERE p.category_id = ? AND p.product_id != ?
        LIMIT 4
    ");
        $stmt->bind_param("ii", $category_id, $exclude_product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $related_products = [];
        while ($row = $result->fetch_assoc()) {
            $related_products[] = $row;
        }
        return $related_products;
    }

    // Function to fetch the most recent products
    public function getRecentProducts($limit = 3)
    {
        $stmt = $this->db->conn->prepare("
        SELECT p.*, c.name AS category_name 
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.category_id
        ORDER BY p.product_id DESC
        LIMIT ?
    ");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        $recent_products = [];
        while ($row = $result->fetch_assoc()) {
            $recent_products[] = $row;
        }
        return $recent_products;
    }
    // Function to fetch the product images
    public function getProductImages($product_id)
    {
        $stmt = $this->db->conn->prepare("
        SELECT image_path 
        FROM product_images 
        WHERE product_id = ?
    ");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $images = [];
        while ($row = $result->fetch_assoc()) {
            $images[] = $row['image_path'];
        }
        return $images;
    }

    // Add product to cart
    public function addToCart($product_id, $quantity)
    {
        // Initialize cart if not already set
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $product_id = (int)$product_id;
        $quantity = (int)$quantity;

        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }

        return true;
    }

    public function removeFromCart($product_id)
    {
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);

            // Force immediate session update
            session_write_close();
            session_start();

            // Clear cart completely if empty
            if (empty($_SESSION['cart'])) {
                unset($_SESSION['cart']);
            }

            // Additional cleanup of any invalid entries
            $_SESSION['cart'] = array_filter($_SESSION['cart'], function ($quantity) {
                return is_numeric($quantity) && $quantity > 0;
            });

            return true;
        }
        return false;
    }
    // Update product quantity in cart
    public function updateCartQuantity($product_id, $quantity)
    {
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] = $quantity; // Update quantity
        }
    }

    // Get cart items with product details
    public function getCartItems()
    {
        $cartItems = [];

        if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
            // Clean up cart data first - remove invalid entries
            $_SESSION['cart'] = array_filter($_SESSION['cart'], function ($quantity) {
                return is_numeric($quantity) && $quantity > 0;
            });

            foreach ($_SESSION['cart'] as $product_id => $quantity) {
                $product = $this->getProductById($product_id);
                if ($product) {
                    $product['quantity'] = (int)$quantity;
                    $cartItems[] = $product;
                }
            }
        }

        return $cartItems;
    }

    public function getCategories()
    {
        $query = "SELECT c.category_id, c.name, COUNT(p.product_id) AS product_count
                  FROM categories c
                  LEFT JOIN products p ON c.category_id = p.category_id
                  GROUP BY c.category_id";

        // Execute the query
        $result = $this->db->conn->query($query);

        // Check if the query was successful
        if (!$result) {
            die("Database error: " . $this->db->conn->error);
        }

        // Fetch all rows as an associative array
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Afunction to filter records for shop page
    public function getFilteredProducts($category_ids = [], $min_price = 0, $max_price = 1000)
    {
        // Build the SQL query
        $query = "SELECT * FROM products WHERE price BETWEEN ? AND ?";
        $params = [$min_price, $max_price];

        // Add category filter if categories are selected
        if (!empty($category_ids)) {
            $query .= " AND category_id IN (" . implode(',', array_fill(0, count($category_ids), '?')) . ")";
            $params = array_merge($params, $category_ids);
        }

        // Prepare and execute the query
        $stmt = $this->db->conn->prepare($query);
        if (!$stmt) {
            die("SQL Error: " . $this->db->conn->error);
        }
        $stmt->bind_param(str_repeat('d', count($params)), ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }


    // Add to wishlist with improved error handling
    public function addToWishlist($user_id, $product_id)
    {
        try {
            // First check if already exists
            if ($this->isInWishlist($user_id, $product_id)) {
                return ['status' => 'exists', 'message' => 'Product already in wishlist'];
            }

            $sql = "INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)";
            $stmt = $this->db->conn->prepare($sql);

            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->db->conn->error);
            }

            $stmt->bind_param("ii", $user_id, $product_id);

            if (!$stmt->execute()) {
                // Check for duplicate entry error
                if ($this->db->conn->errno == 1062) {
                    return ['status' => 'exists', 'message' => 'Product already in wishlist'];
                }
                throw new Exception("Execute failed: " . $stmt->error);
            }

            return ['status' => 'success', 'message' => 'Added to wishlist'];
        } catch (Exception $e) {
            error_log("Wishlist Error: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // Remove from wishlist with improved error handling
    public function removeFromWishlist($user_id, $product_id)
    {
        try {
            $sql = "DELETE FROM wishlist WHERE user_id = ? AND product_id = ?";
            $stmt = $this->db->conn->prepare($sql);

            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->db->conn->error);
            }

            $stmt->bind_param("ii", $user_id, $product_id);

            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }

            return ['status' => 'success', 'message' => 'Removed from wishlist'];
        } catch (Exception $e) {
            error_log("Wishlist Error: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // Check if product is in wishlist
    public function isInWishlist($user_id, $product_id)
    {
        try {
            $sql = "SELECT COUNT(*) as count FROM wishlist WHERE user_id = ? AND product_id = ?";
            $stmt = $this->db->conn->prepare($sql);

            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->db->conn->error);
            }

            $stmt->bind_param("ii", $user_id, $product_id);

            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }

            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            return (int)$row['count'] > 0;
        } catch (Exception $e) {
            error_log("Wishlist Error: " . $e->getMessage());
            return false;
        }
    }

    // Get wishlist items with product details
    public function getWishlistItems($user_id)
    {
        try {
            $sql = "SELECT p.* FROM products p 
                JOIN wishlist w ON p.product_id = w.product_id 
                WHERE w.user_id = ?";
            $stmt = $this->db->conn->prepare($sql);

            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->db->conn->error);
            }

            $stmt->bind_param("i", $user_id);

            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }

            $result = $stmt->get_result();
            $items = $result->fetch_all(MYSQLI_ASSOC);

            // Ensure each item has all required fields
            foreach ($items as &$item) {
                if (!isset($item['image'])) {
                    $item['image'] = 'default-product.png';
                }
            }

            return $items;
        } catch (Exception $e) {
            error_log("Wishlist Error: " . $e->getMessage());
            return [];
        }
    }
    // Function to get latest blog posts
    public function getLatestBlogPosts($limit = 1)
    {
        $stmt = $this->db->conn->prepare("
        SELECT *
        FROM blogs b
        JOIN users u ON b.user_id = u.user_id
        WHERE b.status = 'active'
        ORDER BY b.created_at DESC 
        LIMIT ?
        ");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        $posts = [];
        while ($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }
        return $posts;
    }
}
