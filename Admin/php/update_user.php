<?php
require_once __DIR__ . '/../../classes/Database.php';
include('User.php');

// Initialize variables
$user_data = null;
$error_message = '';

// Get user data for editing
if (isset($_GET['id'])) {
    try {
        $database = new Database();
        $connection = $database->getConnection();
        $userObj = new User($connection);
        $user_data = $userObj->getById($_GET['id']);
        
        if (!$user_data) {
            header("Location: ../user_display.php?msg=User not found");
            exit();
        }
    } catch (PDOException $e) {
        $error_message = "Error fetching user: " . $e->getMessage();
    }
}

// Handle form submission
if (isset($_POST['update_user']) && isset($_POST['id'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];
    $password = !empty($_POST['password']) ? $_POST['password'] : null;

    try {
        $database = new Database();
        $connection = $database->getConnection();
        $userObj = new User($connection);
        
        if ($userObj->update($id, $name, $email, $phone, $role, $password)) {
            header("Location: ../user_display.php?msg=User updated successfully");
            exit();
        } else {
            $error_message = "Failed to update user";
        }
    } catch (PDOException $e) {
        $error_message = "Error updating user: " . $e->getMessage();
    }
}

// If no user data and no error, redirect back
if (!$user_data && !$error_message) {
    header("Location: ../user_display.php?msg=Invalid user ID");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update User</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="container mt-4">
    <!-- Back Button -->
    <a href="../user_display.php" class="btn btn-maroon-outline back-btn">&larr; Back to Users</a>
    
    <h2 class="mb-4">Update User</h2>

    <!-- Error Message -->
    <?php if ($error_message): ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>

    <?php if ($user_data): ?>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <form method="POST" action="">
                <input type="hidden" name="id" value="<?= htmlspecialchars($user_data['ID']) ?>">

                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           class="form-control" 
                           value="<?= htmlspecialchars($user_data['Name']) ?>" 
                           required 
                           minlength="2" 
                           maxlength="100">
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           class="form-control" 
                           value="<?= htmlspecialchars($user_data['Email']) ?>" 
                           required>
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="tel" 
                           id="phone" 
                           name="phone" 
                           class="form-control" 
                           value="<?= htmlspecialchars($user_data['PhoneNumber']) ?>" 
                           required 
                           pattern="[0-9]{10}"
                           title="Please enter a 10-digit phone number">
                </div>

                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="registered user" <?= ($user_data['Role'] == 'registered user') ? 'selected' : '' ?>>Registered User</option>
                        <option value="administrator" <?= ($user_data['Role'] == 'administrator') ? 'selected' : '' ?>>Administrator</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">New Password (Leave blank to keep current)</label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="form-control" 
                           minlength="6"
                           placeholder="Enter new password or leave blank">
                    <div class="form-text">Leave empty to keep the current password</div>
                </div>

                <button type="submit" name="update_user" class="btn btn-maroon w-100">Update User</button>

            </form>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const phone = document.getElementById('phone').value.trim();
    
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
    
    if (!/^[0-9]{10}$/.test(phone)) {
        e.preventDefault();
        alert('Please enter a valid 10-digit phone number.');
        return false;
    }
});
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>