<?php
// Toast container and messages
?>
<!-- Toast Container -->
<div class="toast-container position-fixed top-0 end-0 p-3">
    <?php if (isset($_SESSION['error_msg'])): ?>
    <div class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-x-circle-fill me-2"></i>
                <?= htmlspecialchars($_SESSION['error_msg']) ?>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
    <?php 
    unset($_SESSION['error_msg']);
    endif; ?>
    
    <?php if (isset($_SESSION['success_msg'])): ?>
    <div class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?= htmlspecialchars($_SESSION['success_msg']) ?>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
    <?php 
    unset($_SESSION['success_msg']);
    endif; ?>
</div>

<!-- Toast Initialization Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all toasts
    const toastElList = document.querySelectorAll('.toast');
    const toastList = [...toastElList].map(toastEl => {
        const toast = new bootstrap.Toast(toastEl, {
            autohide: true,
            delay: 5000 // Auto hide after 5 seconds
        });
        toast.show();
        return toast;
    });
});
</script>
