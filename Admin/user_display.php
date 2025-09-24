<?php
require_once __DIR__ . '/../classes/Database.php';
include('php/User.php');

try {
    $database = new Database();
    $connection = $database->getConnection();
    $userObj = new User($connection);
    $users = $userObj->getAll();
} catch (PDOException $e) {
    die("Error fetching users: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Registered Users</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container mt-4">
    <!-- Back Button -->
    <a href="admin.html" class="btn btn-maroon-outline back-btn">&larr; Back</a>

    <!-- Page Title -->
    <h1 class="text-center fw-bold mb-4">Registered Users</h1>

    <!-- Success/Error Messages -->
    <?php if (isset($_GET['msg'])): ?>
        <?php
        $msg = $_GET['msg'];
        $isDelete = stripos($msg, 'delete') !== false;
        $alertClass = $isDelete ? 'alert-danger' : 'alert-success';
        ?>
        <div class="alert <?= $alertClass ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Add Button -->
    <div class="d-flex justify-content-end mb-3">
        <button class="btn btn-maroon" data-bs-toggle="modal" data-bs-target="#addModal">Add User</button>
    </div>

    <!-- User Table -->
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone Number</th>
            <th>Role</th>
            <th>Update</th>
            <th>Delete</th>
          </tr>
        </thead>
        <tbody>
        <?php if (!empty($users)): ?>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['ID']) ?></td>
                    <td><?= htmlspecialchars($user['Name']) ?></td>
                    <td><?= htmlspecialchars($user['Email']) ?></td>
                    <td><?= htmlspecialchars($user['PhoneNumber']) ?></td>
                    <td>
                        <span class="badge <?= $user['Role'] === 'administrator' ? 'bg-danger' : 'bg-primary' ?>">
                            <?= htmlspecialchars($user['Role']) ?>
                        </span>
                    </td>
                    <td>
                        <a href="php/update_user.php?id=<?= $user['ID'] ?>" class="btn btn-success btn-sm">Update</a>
                    </td>
                    <td>
                        <!-- Delete Button triggers Modal -->
                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $user['ID'] ?>">
                            Delete
                        </button>

                        <!-- Delete Confirmation Modal -->
                        <div class="modal fade" id="deleteModal<?= $user['ID'] ?>" tabindex="-1" aria-labelledby="deleteLabel<?= $user['ID'] ?>" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="deleteLabel<?= $user['ID'] ?>">Confirm Delete</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body">
                                Are you sure you want to delete user <strong><?= htmlspecialchars($user['Name']) ?></strong>?
                                <br><small class="text-muted">This action cannot be undone.</small>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <a href="php/delete_user.php?id=<?= $user['ID'] ?>" class="btn btn-danger">Delete</a>
                              </div>
                            </div>
                          </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" class="text-center text-muted">No users found.</td>
            </tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

<!-- Add User Modal -->
<form action="php/insert_user.php" method="POST">
    <div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" required minlength="2" maxlength="100">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" name="phone" class="form-control" required pattern="[0-9]{10}" 
                               title="Please enter a 10-digit phone number" placeholder="0771234567">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select" required>
                            <option value="">Select role...</option>
                            <option value="registered user">Registered User</option>
                            <option value="administrator">Administrator</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_user" class="btn btn-maroon w-100">Add User</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
// Form validation
document.querySelector('#addModal form').addEventListener('submit', function(e) {
    const name = this.querySelector('input[name="name"]').value.trim();
    const email = this.querySelector('input[name="email"]').value.trim();
    const password = this.querySelector('input[name="password"]').value;
    const phone = this.querySelector('input[name="phone"]').value.trim();
    const role = this.querySelector('select[name="role"]').value;
    
    if (name.length < 2) {
        e.preventDefault();
        alert('Name must be at least 2 characters long.');
        return false;
    }
    
    if (!email.includes('@')) {
        e.preventDefault();
        alert('Please enter a valid email address.');
        return false;
    }
    
    if (password.length < 6) {
        e.preventDefault();
        alert('Password must be at least 6 characters long.');
        return false;
    }
    
    if (!/^[0-9]{10}$/.test(phone)) {
        e.preventDefault();
        alert('Please enter a valid 10-digit phone number.');
        return false;
    }
    
    if (!role) {
        e.preventDefault();
        alert('Please select a role.');
        return false;
    }
});
</script>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
