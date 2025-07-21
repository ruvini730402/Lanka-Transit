<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect if not logged in
    exit();
}

$username = htmlspecialchars($_SESSION['username']);

// Example: you may fetch user data from DB here for pre-filling forms
// For now, placeholders are used.

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Settings - LankaTransit</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  
  <link rel="stylesheet" href="UserDashboard.css" />
  <link rel="stylesheet" href="Setting.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
</head>
<body>
  <div class="container">
    <div class="sidebar">
      <!-- Your existing sidebar code here -->
      <div class="logo">
        <img src="uploads/dd.png" alt="LankaTransit Logo" />
      </div>
      <hr />
      <div class="user-profile">
        <div class="user-icon">
          <img src="uploads/rosalette.jpg" alt="User Icon" />
        </div>
        <p class="username"><?= $username ?></p>
      </div>
      <nav class="navigation">
        <ul>
          <li><a href="UserDashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
          <li><a href="UserFeedbackForm.php"><i class="fas fa-comment-alt"></i> Feedback</a></li>
          <li><a href="Report_Incidents.php"><i class="fas fa-exclamation-triangle"></i> Report Incident</a></li>
          <li><a href="#"><i class="fas fa-bullhorn"></i> Announcements</a></li>
          <li class="active"><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
        </ul>
      </nav>
      <div class="logout">
        <a href="Logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
      </div>
    </div>

    <div class="main-content">
      

      <!-- Profile Information -->
      <section class="settings-section">
        <h2>Profile Information</h2>
        <form action="update_profile.php" method="post" enctype="multipart/form-data">
          <label for="name">Name</label>
          <input type="text" id="name" name="name" value="John Doe" required />

          <label for="email">Email</label>
          <input type="email" id="email" name="email" value="john@example.com" required />

          <label for="profile_picture">Profile Picture</label>
          <input type="file" id="profile_picture" name="profile_picture" accept="image/*" />

          <button type="submit" class="btn-primary">Save Changes</button>
        </form>
      </section>

      

      <!-- Privacy Settings -->
      <section class="settings-section">
        <h2>Privacy Settings</h2>
        <form action="update_privacy.php" method="post">
          <label>
            <input type="radio" name="profile_visibility" value="public" checked />
            Public (Visible to everyone)
          </label><br />
          <label>
            <input type="radio" name="profile_visibility" value="private" />
            Private (Only visible to you)
          </label><br />

          <button type="submit" class="btn-primary">Update Privacy</button>
        </form>
      </section>

      <!-- Account Management -->
<section class="settings-section">
  <h2>Account Management</h2>
  <form action="manage_account.php" method="post" onsubmit="return confirmAccountAction();">
    <label>
      <input type="radio" name="account_action" value="deactivate" required />
      Deactivate Account (You can reactivate it later by logging in)
    </label><br>

    <label>
      <input type="radio" name="account_action" value="delete" required />
      Delete Account Permanently (This cannot be undone)
    </label><br>

    <button type="submit" class="btn-danger" style="margin-top: 10px;">Confirm Action</button>
  </form>
</section>
      
      <!-- Custom Confirmation Dialog -->
<div id="account-confirm-dialog" class="custom-dialog">
  <div class="dialog-box">
    <p id="dialog-message"></p>
    <div class="dialog-actions">
      <button id="dialog-confirm" class="btn-danger">Yes, Proceed</button>
      <button id="dialog-cancel" class="btn-secondary">Cancel</button>
    </div>
  </div>
</div>


    </div>
  </div>
    <script>
document.querySelector('form[action="manage_account.php"]').addEventListener('submit', function (e) {
  e.preventDefault(); // Prevent normal form submit

  const selected = document.querySelector('input[name="account_action"]:checked');
  if (!selected) {
    alert("Please select an action first.");
    return;
  }

  const message = selected.value === "delete"
    ? "⚠ Are you sure you want to permanently <strong>DELETE</strong> your account?"
    : "Are you sure you want to <strong>DEACTIVATE</strong> your account?";

  document.getElementById("dialog-message").innerHTML = message;
  document.getElementById("account-confirm-dialog").classList.add("show");

  // On Confirm
  document.getElementById("dialog-confirm").onclick = function () {
    document.getElementById("account-confirm-dialog").classList.remove("show");
    e.target.submit(); // Submit original form
  };

  // On Cancel
  document.getElementById("dialog-cancel").onclick = function () {
    document.getElementById("account-confirm-dialog").classList.remove("show");
  };
});
</script>

</body>
</html>
