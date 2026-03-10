<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
// Define base URL for your project
// Adjust '/Elearning2/' to your project folder name
define('BASE_URL', '/Elearning2/');
?>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?= BASE_URL ?>index.php">CourseHub</a>

    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#menu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="menu">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="<?= BASE_URL ?>index.php"><i class="bi bi-house"></i> Home</a>
        </li>

        <?php if (isset($_SESSION['user_id'])): ?>
          <?php if ($_SESSION['role'] === 'admin'): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?= BASE_URL ?>admin/admin_dashboard.php">Courses Manage</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?= BASE_URL ?>admin/user_manage.php">User Manage</a>
            </li>
          <?php else: ?>
            <li class="nav-item">
              <a class="nav-link" href="<?= BASE_URL ?>my_courses.php">My Courses</a>
            </li>
          <?php endif; ?>

          <li class="nav-item">
            <a class="nav-link" href="<?= BASE_URL ?>logout.php">Logout (<?= htmlspecialchars($_SESSION['full_name']) ?>)</a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#signupModal">Sign Up</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#loginModal">Log In</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<!-- Display messages -->
<?php if (isset($_SESSION['error'])): ?>
  <div class="alert alert-danger text-center" id="message"><?= $_SESSION['error'];
                                                            unset($_SESSION['error']); ?></div>
<?php endif; ?>

<?php if (isset($_SESSION['success'])): ?>
  <div class="alert alert-success text-center" id="message"><?= $_SESSION['success'];
                                                            unset($_SESSION['success']); ?></div>
<?php endif; ?>

<script>
  setTimeout(() => {
    const msg = document.getElementById('message');
    if (msg) msg.style.display = 'none';
  }, 6000);
</script>

<!-- Signup Modal -->
<div class="modal fade" id="signupModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="post" action="signup_process.php">
        <div class="modal-header">
          <h5 class="modal-title">Sign Up</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Full Name</label>
            <input type="text" name="full_name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-outline-primary">Sign Up</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="post" action="login_process.php">
        <div class="modal-header">
          <h5 class="modal-title">Log In</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-outline-primary">Log In</button>
        </div>
      </form>
    </div>
  </div>
</div>