<?php
require 'connection/db.php';

// Fetch all courses for listing
$sql = "SELECT * FROM courses ORDER BY created_at DESC";
$result = $conn->query($sql);

// Fetch last 3 courses for slider
$sliderSql = "SELECT * FROM courses ORDER BY created_at DESC LIMIT 3";
$sliderResult = $conn->query($sliderSql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Programming Courses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background: #f4f6f9;
            font-family: Segoe UI;
        }

        .navbar {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .course-card {
            border: none;
            border-radius: 14px;
            overflow: hidden;
            transition: 0.3s;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        }

        .course-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
        }

        .course-card img {
            height: 200px;
            object-fit: cover;
        }

        .youtube-btn {
            color: #ff0000;
            font-weight: 500;
            text-decoration: none;
        }

        .youtube-btn:hover {
            color: #cc0000;
        }

        .enroll-btn {
            background: #198754;
            color: white;
            border: none;
            padding: 6px 14px;
            border-radius: 6px;
        }

        .enroll-btn:hover {
            background: #157347;
        }

        .carousel-item img {
            height: 400px;
            object-fit: cover;
            border-radius: 14px;
        }

        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            filter: invert(1);
            /* inverts the default white arrow to black */
        }
    </style>
</head>

<body>

    <?php include 'headers/header.php'; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger text-center" id="alertMessage"><?= $_SESSION['error'];
        unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success text-center" id="alertMessage"><?= $_SESSION['success'];
        unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const alertBox = document.getElementById('alertMessage');
            if (alertBox) {
                setTimeout(() => {
                    alertBox.style.transition = "opacity 0.5s";
                    alertBox.style.opacity = '0';
                    setTimeout(() => alertBox.remove(), 500);
                }, 6000);
            }
        });
    </script>

    <div class="container mt-5">

        <!-- Slider for last 3 courses -->
        <?php if ($sliderResult->num_rows > 0): ?>
            <div id="courseCarousel" class="carousel slide mb-5" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php $active = 'active'; ?>
                    <?php while ($slide = $sliderResult->fetch_assoc()): ?>
                        <div class="carousel-item <?= $active ?>">
                            <img src="uploads/<?= !empty($slide['image_url']) ? htmlspecialchars($slide['image_url']) : 'default.png' ?>" class="d-block w-100" alt="<?= htmlspecialchars($slide['name']) ?>">
                            <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-2">
                                <h5><?= htmlspecialchars($slide['name']) ?></h5>
                                <p><?= htmlspecialchars($slide['description']) ?></p>
                            </div>
                        </div>
                    <?php $active = '';
                    endwhile; ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#courseCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#courseCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
            </div>
        <?php endif; ?>

        <!-- All courses listing -->
        <div class="text-center mb-5">
            <h2 class="fw-bold">Programming Courses</h2>
            <p class="text-muted">Choose your favorite programming course</p>
        </div>

        <div class="row g-4">
            <?php while ($course = $result->fetch_assoc()): ?>
                <div class="col-md-4">
                    <div class="card course-card">
                        <img src="uploads/<?= !empty($course['image_url']) ? htmlspecialchars($course['image_url']) : 'default.png' ?>" class="card-img-top" alt="<?= htmlspecialchars($course['name']) ?>">
                        <div class="card-body">
                            <h5><?= $course['name'] ?></h5>
                            <p class="text-muted small"><?= $course['description'] ?></p>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-info"><?= $course['category'] ?></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <a class="youtube-btn watch-video" data-video="<?= $course['youtube_link'] ?>" data-bs-toggle="modal" data-bs-target="#videoModal">
                                    <i class="bi bi-youtube"></i> Watch
                                </a>
                                <?php
                                if (isset($_SESSION['user_id']) && (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin')):
                                ?>
                                    <a href="enroll.php?course_id=<?= $course['id'] ?>" class="enroll-btn">
                                        <i class="bi bi-mortarboard"></i> Enroll
                                    </a>
                                <?php elseif (!isset($_SESSION['user_id'])): ?>
                                    <button class="enroll-btn" onclick="alert('Please log in to enroll!')">
                                        <i class="bi bi-mortarboard"></i> Enroll
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- YouTube Modal -->
    <div class="modal fade" id="videoModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Course Video</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="ratio ratio-16x9">
                        <iframe id="youtubeVideo" src="" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'headers/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const videoLinks = document.querySelectorAll(".watch-video");
        const videoFrame = document.getElementById("youtubeVideo");

        videoLinks.forEach(link => {
            link.addEventListener("click", function() {
                videoFrame.src = this.getAttribute("data-video");
            });
        });

        const modal = document.getElementById('videoModal');
        modal.addEventListener('hidden.bs.modal', function() {
            videoFrame.src = "";
        });
    </script>

</body>

</html>