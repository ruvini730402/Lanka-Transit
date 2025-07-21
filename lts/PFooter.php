<?php
// footer.php
?>
    <!-- Footer -->
<footer class="py-4" style="margin-top:10px; background-color: #060725; color: #fff;">
        <div class="container">
            <div class="row">
                <p style="text-align: center; width: 100%;">&copy; 2025 Transit. All rights reserved.</p>            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('searchForm').addEventListener('submit', function(e) {
            const origin = document.getElementById('origin').value;
            const destination = document.getElementById('destination').value;
            const travelDate = document.getElementById('travel_date').value;

            if (origin === destination && origin !== '') {
                e.preventDefault();
                alert('Origin and destination cannot be the same!');
                return false;
            }

            const today = new Date().toISOString().split('T')[0];
            if (travelDate < today) {
                e.preventDefault();
                alert('Please select a valid travel date!');
                return false;
            }
        });

        document.getElementById('travel_date').value = new Date().toISOString().split('T')[0];
    </script>
</body>
</html>
