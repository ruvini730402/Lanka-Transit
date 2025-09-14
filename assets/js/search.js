// Search Page JavaScript Functionality

document.addEventListener('DOMContentLoaded', function() {
    initializeSearchPage();
});

function initializeSearchPage() {
    // Auto-apply filters on sort change
    const sortSelect = document.querySelector('select[name="sort_by"]');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            document.getElementById('filtersForm').submit();
        });
    }

    // Enhanced Navigation Scroll Effect
    window.addEventListener('scroll', function() {
        const navbar = document.getElementById('mainNavbar');
        if (navbar) {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        }
    });

    // Form validation before submission
    const form = document.getElementById('filtersForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validateFareRange() || !validateTimeRange()) {
                e.preventDefault();
                return false;
            }
        });
    }
}

// Clear all filters
function clearFilters() {
    // Clear all filter inputs
    const form = document.getElementById('filtersForm');
    if (form) {
        // Reset all inputs except the hidden search criteria
        const inputs = form.querySelectorAll('input:not([type="hidden"]), select');
        inputs.forEach(input => {
            if (input.name !== 'sort_by') {
                input.value = '';
            } else {
                input.value = 'departure_early';
            }
        });
        
        // Submit the form to apply cleared filters
        form.submit();
    }
}

// Validate fare range
function validateFareRange() {
    const minFare = document.querySelector('input[name="min_fare"]');
    const maxFare = document.querySelector('input[name="max_fare"]');
    
    if (minFare && maxFare) {
        const min = parseFloat(minFare.value) || 0;
        const max = parseFloat(maxFare.value) || Infinity;
        
        if (min > max && max > 0) {
            alert('Minimum fare cannot be greater than maximum fare.');
            return false;
        }
    }
    return true;
}

// Validate time range
function validateTimeRange() {
    const timeFrom = document.querySelector('input[name="departure_time_from"]');
    const timeTo = document.querySelector('input[name="departure_time_to"]');
    
    if (timeFrom && timeTo && timeFrom.value && timeTo.value) {
        if (timeFrom.value > timeTo.value) {
            alert('Start time cannot be later than end time.');
            return false;
        }
    }
    return true;
}
