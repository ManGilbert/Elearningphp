<?php
require 'connection/db.php';
session_start();

// Only logged-in students
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Please log in to view your courses!";
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Pagination
$limit = 5;
$page = intval($_GET['page'] ?? 1);
$start = ($page - 1) * $limit;

// Total enrolled courses
$totalResult = $conn->prepare("SELECT COUNT(*) AS total FROM my_courses WHERE user_id=?");
$totalResult->bind_param("i", $user_id);
$totalResult->execute();
$totalResult->bind_result($totalCourses);
$totalResult->fetch();
$totalResult->close();

$totalPages = ceil($totalCourses / $limit);

// Fetch enrolled courses with course details
$sql = "SELECT c.*, m.progress 
        FROM my_courses m
        INNER JOIN courses c ON m.course_id = c.id
        WHERE m.user_id = ?
        ORDER BY m.enrolled_at DESC
        LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $user_id, $start, $limit);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Courses</title>
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
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}
.progress-bar {
    transition: width 0.5s ease;
}
</style>
</head>
<body>

<?php include 'headers/header.php'; ?>

<div class="container mt-5">
    <h2 class="mb-4">My Enrolled Courses</h2>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <?php if ($totalCourses == 0): ?>
        <div class="alert alert-info text-center">
            You are not enrolled in any courses yet. Browse <a href="index.php">courses here</a>!
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php while ($course = $result->fetch_assoc()): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm course-card"
                     data-bs-toggle="modal" data-bs-target="#courseModal"
                     data-name="<?= htmlspecialchars($course['name']) ?>"
                     data-description="<?= htmlspecialchars($course['description']) ?>"
                     data-category="<?= htmlspecialchars($course['category']) ?>"
                     data-youtube="<?= htmlspecialchars($course['youtube_link']) ?>"
                     data-image="<?= htmlspecialchars($course['image_url']) ?>"
                     data-progress="<?= $course['progress'] ?>">
                    <img src="uploads/<?= !empty($course['image_url']) ? htmlspecialchars($course['image_url']) : 'default.png' ?>" class="card-img-top" alt="<?= htmlspecialchars($course['name']) ?>">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <h5 class="card-title"><?= htmlspecialchars($course['name']) ?></h5>
                        <p class="text-muted"><?= htmlspecialchars($course['category']) ?></p>
                        <div class="progress mt-2">
                            <div class="progress-bar bg-success" role="progressbar" style="width: <?= $course['progress'] ?>%;" aria-valuenow="<?= $course['progress'] ?>" aria-valuemin="0" aria-valuemax="100">
                                <?= $course['progress'] ?>%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <!-- Pagination -->
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= ($i==$page)?'active':'' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<!-- Video Modal -->
<div class="modal fade" id="courseModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Course Video</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="ratio ratio-16x9">
                    <iframe id="youtubeVideo" src="" allowfullscreen></iframe>
                </div>
                <div class="p-3">
                    <p><strong>Description:</strong> <span id="courseDescription"></span></p>
                    <p><strong>Category:</strong> <span id="courseCategory"></span></p>
                    <p><strong>Progress:</strong> <span id="courseProgress"></span>%</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'headers/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const courseCards = document.querySelectorAll('.course-card');
const youtubeVideo = document.getElementById('youtubeVideo');
const modalTitle = document.getElementById('modalTitle');
const courseDescription = document.getElementById('courseDescription');
const courseCategory = document.getElementById('courseCategory');
const courseProgress = document.getElementById('courseProgress');

courseCards.forEach(card => {
    card.addEventListener('click', () => {
        modalTitle.innerText = card.dataset.name;
        youtubeVideo.src = card.dataset.youtube;
        courseDescription.innerText = card.dataset.description;
        courseCategory.innerText = card.dataset.category;
        courseProgress.innerText = card.dataset.progress;
    });
});

const modal = document.getElementById('courseModal');
modal.addEventListener('hidden.bs.modal', () => {
    youtubeVideo.src = '';
});
</script>

</body>
</html>