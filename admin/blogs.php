<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once __DIR__ . '/../includes/db/functions.php';

if (!isset($_SESSION["admin_email"])) {
    header("Location: login.php");
    exit;
}

$functions = new Functions();
$conn = $functions->getDatabaseConnection();
$errors = [];
$success = "";

// Set Default Pagination
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Search Feature
$search = isset($_GET['search']) ? trim($_GET['search']) : "";
$searchQuery = "";
if (!empty($search)) {
    $searchQuery = "WHERE title LIKE ? OR meta_keywords LIKE ? OR status LIKE ?";
}

// Count Total Blogs
$countQuery = "SELECT COUNT(*) AS total FROM blogs $searchQuery";
$stmt = $conn->prepare($countQuery);

if (!empty($search)) {
    $searchTerm = "%$search%";
    $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
}

$stmt->execute();
$countResult = $stmt->get_result();
$totalBlogs = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalBlogs / $limit);

// Fetch Blogs
$query = "SELECT id, title, slug, status FROM blogs $searchQuery ORDER BY id DESC LIMIT ?, ?";
$stmt = $conn->prepare($query);

if (!empty($search)) {
    $stmt->bind_param("ssii", $searchTerm, $searchTerm, $searchTerm, $offset, $limit);
} else {
    $stmt->bind_param("ii", $offset, $limit);
}

$stmt->execute();
$result = $stmt->get_result();
$blogs = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Blogs | Admin Panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body>
    <?php include "sidebar.php"; ?>

    <div class="content">
        <div class="dashboard-header">
            <h2>Manage Blogs</h2>
        </div>

        <div class="container mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>All Blogs</h4>
                <a href="add_blog.php" class="btn btn-primary">➕ Add New Blog</a>
            </div>

            <form method="GET" class="mb-3 d-flex">
                <input type="text" name="search" class="form-control me-2" placeholder="Search by Title, Keywords, or Status" value="<?php echo htmlspecialchars($search); ?>">
                <select name="limit" class="form-select me-2">
                    <option value="10" <?php echo ($limit == 10) ? 'selected' : ''; ?>>Show 10</option>
                    <option value="20" <?php echo ($limit == 20) ? 'selected' : ''; ?>>Show 20</option>
                    <option value="50" <?php echo ($limit == 50) ? 'selected' : ''; ?>>Show 50</option>
                </select>
                <button type="submit" class="btn btn-success">Filter</button>
            </form>

            <div class="card">
                <div class="card-header bg-dark text-white">Blog List</div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Slug</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($blogs as $blog): ?>
                                <tr>
                                    <td><?php echo $blog['id']; ?></td>
                                    <td><?php echo htmlspecialchars($blog['title']); ?></td>
                                    <td><?php echo htmlspecialchars($blog['slug']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $blog['status'] == 'active' ? 'success' : 'danger'; ?>">
                                            <?php echo ucfirst($blog['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="edit_blog.php?id=<?php echo $blog['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                        <a href="toggle_blog.php?id=<?php echo $blog['id']; ?>" class="btn btn-secondary btn-sm">
                                            <?php echo $blog['status'] == 'active' ? 'Deactivate' : 'Activate'; ?>
                                        </a>
                                        <a href="delete_blog.php?id=<?php echo $blog['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($blogs)): ?>
                                <tr>
                                    <td colspan="5" class="text-center">No blogs found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <nav>
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                    <a class="page-link" href="?search=<?php echo urlencode($search); ?>&limit=<?php echo $limit; ?>&page=<?php echo $i; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>

                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
