<?php
require '../connection/db.php';
session_start();

// Only admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Access denied!";
    header("Location: index.php");
    exit();
}

// Pagination
$limit = 5;
$page = intval($_GET['page'] ?? 1);
$start = ($page - 1) * $limit;

// Total courses
$totalResult = $conn->query("SELECT COUNT(*) AS total FROM courses");
$totalRow = $totalResult->fetch_assoc();
$totalCourses = $totalRow['total'];
$totalPages = ceil($totalCourses / $limit);

// Fetch courses
$sql = "SELECT courses.*, users.full_name AS creator 
        FROM courses 
        LEFT JOIN users ON courses.created_by = users.id
        ORDER BY created_at DESC
        LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $start, $limit);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Courses Manage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .course-card {
            border-radius: 12px;
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
        }

        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>

<body>

    <?php include '../headers/header.php'; ?>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Courses Manage</h2>
            <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                <i class="bi bi-plus-circle"></i> Add Course
            </button>
        </div>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error'];
                                            unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success'];
                                                unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <div class="row g-4">
            <?php while ($course = $result->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm course-card"
                        data-bs-toggle="modal" data-bs-target="#courseModal"
                        data-id="<?= $course['id'] ?>"
                        data-name="<?= htmlspecialchars($course['name']) ?>"
                        data-description="<?= htmlspecialchars($course['description']) ?>"
                        data-category="<?= htmlspecialchars($course['category']) ?>"
                        data-youtube="<?= htmlspecialchars($course['youtube_link']) ?>"
                        data-image="<?= htmlspecialchars($course['image_url']) ?>"
                        data-creator="<?= htmlspecialchars($course['creator'] ?? 'Unknown') ?>"
                        data-created="<?= $course['created_at'] ?>">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <h5 class="card-title"><?= htmlspecialchars($course['name']) ?></h5>
                            <p class="card-text text-muted"><?= htmlspecialchars($course['category']) ?></p>
                            <small class="text-secondary mt-auto">Created by: <?= htmlspecialchars($course['creator'] ?? 'Unknown') ?></small>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Pagination -->
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>

    <!-- Add Course Modal -->
    <div class="modal fade" id="addCourseModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="post" action="add_course.php" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Course</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Course Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Category</label>
                            <input type="text" name="category" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>YouTube Link</label>
                            <input type="url" name="youtube_link" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Image</label>
                            <input type="file" name="image" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-outline-success">Add Course</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="courseModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Course Name</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs" id="courseTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="view-tab" data-bs-toggle="tab" href="#view" role="tab">View</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="edit-tab" data-bs-toggle="tab" href="#edit" role="tab">Edit</a>
                        </li>
                    </ul>
                    <div class="tab-content mt-3">
                        <!-- View Tab -->
                        <div class="tab-pane fade show active" id="view" role="tabpanel">
                            <img id="course-image" src="" class="img-fluid mb-3 rounded" alt="">
                            <p><strong>Description:</strong> <span id="course-description"></span></p>
                            <p><strong>Category:</strong> <span id="course-category"></span></p>
                            <p><strong>Creator:</strong> <span id="course-creator"></span></p>
                            <p><strong>Created At:</strong> <span id="course-created"></span></p>
                            <p><strong>YouTube:</strong> <a id="course-youtube" href="#" target="_blank">Watch</a></p>
                            <a href="#" id="deleteBtn" class="btn btn-outline-danger mt-2" onclick="return confirm('Are you sure?');"><i class="bi bi-trash"></i> Delete</a>
                        </div>
                        <!-- Edit Tab -->
                        <div class="tab-pane fade" id="edit" role="tabpanel">
                            <form id="editCourseForm" method="post" action="edit_course.php" enctype="multipart/form-data">
                                <input type="hidden" name="id" id="edit-course-id">
                                <div class="mb-3">
                                    <label>Course Name</label>
                                    <input type="text" name="name" id="edit-course-name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label>Description</label>
                                    <textarea name="description" id="edit-course-description" class="form-control" rows="4" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label>Category</label>
                                    <input type="text" name="category" id="edit-course-category" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label>YouTube Link</label>
                                    <input type="text" name="youtube_link" id="edit-course-youtube" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label>Image</label>
                                    <input type="file" name="image" class="form-control">
                                    <small class="text-muted">Current Image: <span id="edit-current-image"></span></small>
                                </div>
                                <button type="submit" class="btn btn-outline-success"><i class="bi bi-check-lg"></i> Save Changes</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const courseCards = document.querySelectorAll('.course-card');

        courseCards.forEach(card => {
            card.addEventListener('click', () => {
                // Fill view tab
                document.getElementById('modalTitle').innerText = card.dataset.name;
                document.getElementById('course-image').src = card.dataset.image ? '../uploads/' + card.dataset.image : 'uploads/default.png';
                document.getElementById('course-description').innerText = card.dataset.description;
                document.getElementById('course-category').innerText = card.dataset.category;
                document.getElementById('course-creator').innerText = card.dataset.creator;
                document.getElementById('course-created').innerText = card.dataset.created;
                document.getElementById('course-youtube').href = card.dataset.youtube;

                // Fill edit tab
                document.getElementById('edit-course-id').value = card.dataset.id;
                document.getElementById('edit-course-name').value = card.dataset.name;
                document.getElementById('edit-course-description').value = card.dataset.description;
                document.getElementById('edit-course-category').value = card.dataset.category;
                document.getElementById('edit-course-youtube').value = card.dataset.youtube;
                document.getElementById('edit-current-image').innerText = card.dataset.image ? card.dataset.image : 'default.png';

                // Delete button
                document.getElementById('deleteBtn').href = 'delete_course.php?id=' + card.dataset.id;
            });
        });
    </script>
    <?php include '../headers/footer.php'; ?>

</body>

</html>