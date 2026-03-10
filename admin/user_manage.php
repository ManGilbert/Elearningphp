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

// Total users
$totalResult = $conn->query("SELECT COUNT(*) AS total FROM users");
$totalRow = $totalResult->fetch_assoc();
$totalUsers = $totalRow['total'];
$totalPages = ceil($totalUsers / $limit);

// Fetch users
$sql = "SELECT * FROM users WHERE role = 'student' ORDER BY created_at DESC LIMIT ?, ?";
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
<title>Admin - Users Manage</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<style>
.user-card {
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
    border-radius: 10px;
}
.user-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.12);
}
</style>
</head>
<body>
<?php include '../headers/header.php'; ?>

<div class="container mt-5">
    <h2 class="mb-4">Users Manage</h2>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <div class="row g-4">
        <?php while ($user = $result->fetch_assoc()): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm user-card"
                 data-bs-toggle="modal" data-bs-target="#userModal"
                 data-id="<?= $user['id'] ?>"
                 data-name="<?= htmlspecialchars($user['full_name']) ?>"
                 data-email="<?= htmlspecialchars($user['email']) ?>"
                 data-role="<?= htmlspecialchars($user['role']) ?>"
                 data-status="<?= $user['status'] ?>"
                 data-created="<?= $user['created_at'] ?>">
                <div class="card-body d-flex flex-column justify-content-between">
                    <h5 class="card-title"><?= htmlspecialchars($user['full_name']) ?></h5>
                    <p class="text-muted"><?= htmlspecialchars($user['email']) ?></p>
                    <span class="badge <?= $user['status']=='active'?'bg-success':'bg-danger' ?>"><?= ucfirst($user['status']) ?></span>
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
</div>

<!-- Modal -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalTitle">User Info</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Full Name:</strong> <span id="userName"></span></p>
                <p><strong>Email:</strong> <span id="userEmail"></span></p>
                <p><strong>Role:</strong> <span id="userRole"></span></p>
                <p><strong>Created At:</strong> <span id="userCreated"></span></p>
                <p><strong>Status:</strong> <span id="userStatus"></span></p>
            </div>
            <div class="modal-footer">
                <a href="#" id="toggleStatusBtn" class="btn"></a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const userCards = document.querySelectorAll('.user-card');
const userName = document.getElementById('userName');
const userEmail = document.getElementById('userEmail');
const userRole = document.getElementById('userRole');
const userCreated = document.getElementById('userCreated');
const userStatus = document.getElementById('userStatus');
const toggleStatusBtn = document.getElementById('toggleStatusBtn');

userCards.forEach(card => {
    card.addEventListener('click', () => {
        const id = card.dataset.id;
        const status = card.dataset.status;
        const isActive = status === 'active';

        userName.innerText = card.dataset.name;
        userEmail.innerText = card.dataset.email;
        userRole.innerText = card.dataset.role;
        userCreated.innerText = card.dataset.created;
        userStatus.innerText = card.dataset.status;

        toggleStatusBtn.innerText = isActive ? 'Block User' : 'Unblock User';
        toggleStatusBtn.className = isActive ? 'btn btn-danger' : 'btn btn-success';
        toggleStatusBtn.href = 'toggle_user_status.php?id=' + id + '&action=' + (isActive ? 'block' : 'unblock');
    });
});
</script>
<?php include '../headers/footer.php'; ?>
</body>
</html>