<?php
include('dbcon.php');
include('php/announcement.php');

try {
    $announcementObj = new Announcement($connection);
    $announcements = $announcementObj->getAll();
} catch (PDOException $e) {
    die("Error fetching announcements: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Announcements</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="container mt-4">
    <!-- Back Button -->
    <a href="admin.html" class="btn btn-maroon-outline back-btn">&larr; Back</a>


    <h1 class="text-center mb-4">Announcements</h1>

    <!-- Success Message -->
    <?php if (isset($_GET['msg'])): ?>
    <?php
    $msg = $_GET['msg'];
    $isDelete = stripos($msg, 'delete') !== false; // check if message contains "delete"
    $alertClass = $isDelete ? 'alert-danger' : 'alert-success';
    ?>
    <div class="alert <?= $alertClass ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($msg) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

    

    <!-- Add Button -->
    <div class="d-flex justify-content-end mb-3">
        <button class="btn btn-maroon" data-bs-toggle="modal" data-bs-target="#addModal">Add Announcement</button>
    </div>

    <!-- Announcements Table -->
   <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>Title</th>
                <th>Content</th>
                <th>Posted On</th>
                <th>Update</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($announcements)): ?>
            <?php foreach ($announcements as $a): ?>
                <tr>
                    <td><?= htmlspecialchars($a['title']) ?></td>
                    <td><?= htmlspecialchars($a['content']) ?></td>
                    <td><?= htmlspecialchars($a['posted_date']) ?></td>
                    <td>
                        <a href="php/update_announcement.php?id=<?= $a['id'] ?>" class="btn btn-success btn-sm">Update</a>
                    </td>
                    <td>
                        <!-- Delete Button triggers Modal -->
                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $a['id'] ?>">
                            Delete
                        </button>

                        <!-- Delete Confirmation Modal -->
                        <div class="modal fade" id="deleteModal<?= $a['id'] ?>" tabindex="-1" aria-labelledby="deleteLabel<?= $a['id'] ?>" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">

                              <div class="modal-header">
                                <h5 class="modal-title" id="deleteLabel<?= $a['id'] ?>">Confirm Delete</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>

                              <div class="modal-body">
                                Are you sure you want to delete the announcement ?
                              </div>

                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <a href="php/delete_announcement.php?id=<?= $a['id'] ?>" class="btn btn-danger">Delete</a>
                              </div>

                            </div>
                          </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" class="text-center text-muted">No announcements found.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Add Announcement Modal -->
<form action="php/insert_announcement.php" method="POST">
    <div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Add Announcement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" required minlength="3" maxlength="100">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Content</label>
                        <textarea name="content" class="form-control" rows="4" required minlength="5" maxlength="1000"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" name="add_announcement" class="btn btn-maroon w-100">Add</button>
                </div>

            </div>
        </div>
    </div>
</form>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
